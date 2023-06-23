<?php

namespace Farzai\Viola;

use Farzai\Viola\Contracts\AnswerResolverInterface;
use Farzai\Viola\Contracts\Database\ConnectionInterface;
use Farzai\Viola\Contracts\PromptRepositoryInterface;
use Farzai\Viola\Contracts\ViolaResponseInterface;
use Farzai\Viola\Exceptions\QueryCommandUnsafe;
use Farzai\Viola\Exceptions\UnexpectedResponse;
use Farzai\Viola\Resources\PromptRepository;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Viola
{
    /**
     * The configuration.
     */
    private array $config;

    /**
     * The OpenAI Http client.
     */
    private OpenAi\Client $openAi;

    /**
     * The prompt repository.
     */
    private PromptRepositoryInterface $prompt;

    /**
     * The logger.
     */
    private LoggerInterface $logger;

    /**
     * The answer resolver.
     */
    private AnswerResolverInterface $anwserResolver;

    /**
     * The database connection.
     */
    private ConnectionInterface $database;

    /**
     * Cache the tables and columns.
     */
    private $cache = [
        'platform' => null,
        'tables' => null,
        'columns' => null,
    ];

    /**
     * Create a new Viola builder.
     */
    public static function builder()
    {
        return new Builder();
    }

    /**
     * Create a new Viola instance.
     */
    public function __construct(
        array $config,
        ConnectionInterface $database,
        ?ClientInterface $client,
        ?LoggerInterface $logger,
    ) {
        $logger ??= new NullLogger();

        $this->config = $config;
        $this->database = $database;
        $this->logger = $logger;

        $this->openAi = new OpenAI\Client($client, $logger, $this->config['api_key']);
        $this->prompt = new PromptRepository();
        $this->anwserResolver = new OpenAI\AnswerResolver();
    }

    /**
     * Ask a question.
     */
    public function ask(string $question): ViolaResponseInterface
    {
        $this->logger->info(sprintf('Viola: Asking question "%s"', $question));

        $messages[] = [
            'role' => 'system',
            'content' => $this->buildSystemPrompt($question),
        ];

        $response = $this->askOpenAi($messages);

        $this->ensureQueryCommandIsSafe(
            $queryCommand = $this->resolveQueryCommand($response->json())
        );

        $this->logger->info(sprintf('Viola: Query command "%s"', $queryCommand));

        $results = $this->database->performQuery($queryCommand);

        if (count($results) === 0) {
            return new ViolaResponse($response->json(), $queryCommand, $results);
        }

        // If the number of results is less than 5, we will ask the AI to summarize the results.
        if (count($results) < 5) {
            array_push($messages, [
                'role' => 'assistant',
                'content' => "SQLQuery: \"{$queryCommand}\"",
            ], [
                'role' => 'user',
                'content' => $this->prompt->compile('summary', [
                    'result' => json_encode($results),
                ]),
            ]);

            $response = $this->askOpenAi($messages, 0.5);
        }

        return new ViolaResponse($response->json(), $queryCommand, $results);
    }

    /**
     * Build the system prompt.
     */
    private function buildSystemPrompt(string $question): string
    {
        $tables = $this->getTables();

        if (count($tables) > 5) {
            $this->logger->warning(sprintf(
                'Viola: The number of tables is %d, which is more than 5. This may cause the AI to perform poorly.',
                count($tables)
            ));

            $tables = $this->filterMatchingTables($question, $tables);

            $this->logger->info(sprintf(
                'Viola: After filtering, the number of tables is %d.',
                count($tables)
            ));
        }

        return $this->prompt->compile('query', [
            'tables' => $this->convertTablesToPrompt($tables),
            'platform' => $this->getPlatform(),
            'limit' => $this->config['limit'],
            'question' => $question,
        ]);
    }

    /**
     * Get the tables with columns.
     */
    private function getTables()
    {
        if (empty($this->cache['tables'])) {
            $tables = [];

            $names = $this->database->getTables();

            foreach ($names as $table) {
                $tables[$table] = $this->getColumns($table);
            }

            $this->cache['tables'] = $tables;
        }

        return $this->cache['tables'];
    }

    /**
     * Get the platform.
     */
    private function getPlatform(): string
    {
        if (empty($this->cache['platform'])) {
            $this->cache['platform'] = $this->database->getPlatform();
        }

        return $this->cache['platform'];
    }

    /**
     * Get the columns for the given table.
     */
    private function getColumns(string $table): array
    {
        if (empty($this->cache['columns'][$table])) {
            $columns = $this->database->getColumns($table);

            $exceptWildcardColumns = array_filter($this->config['except']['columns'], function ($column) {
                return str_contains($column, '*');
            });

            $exceptColumns = array_filter($this->config['except']['columns'], function ($column) {
                return ! str_contains($column, '*');
            });

            $filteredColumns = [];
            foreach ($columns as $name => $type) {
                if (in_array($name, $exceptColumns)) {
                    continue;
                }

                foreach ($exceptWildcardColumns as $exceptColumn) {
                    if (str_contains($exceptColumn, $name)) {
                        continue 2;
                    }
                }

                $filteredColumns[$name] = $type;
            }

            $this->cache['columns'][$table] = $filteredColumns;
        }

        return $this->cache['columns'][$table];
    }

    /**
     * Ensure the given query command is safe.
     *
     * @return void
     */
    private function ensureQueryCommandIsSafe(string $query)
    {
        $dangerousCommands = [
            'drop table',
            'delete from',
            'truncate',
            'update',
            'alter table',
            'create table',
            'insert into',
        ];

        foreach ($dangerousCommands as $command) {
            if (strpos(strtolower($query), $command) !== false) {
                throw QueryCommandUnsafe::create($query);
            }
        }
    }

    /**
     * Filter the matching tables.
     *
     * @return array
     */
    private function filterMatchingTables(string $question, array $tables)
    {
        $response = $this->askOpenAi([
            [
                'role' => 'system',
                'content' => $this->prompt->compile('tables', [
                    'tables' => $this->convertTablesToPrompt($tables),
                    'question' => $question,
                ]),
            ],
        ]);

        $names = explode(',', $this->getAssistantMessage($response->json()));

        $names = array_map(fn ($table) => trim($table), $names);
        $names = array_unique($names);

        return array_filter($tables, function ($table) use ($names) {
            return in_array($table, $names);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Ask OpenAI.
     *
     *
     * @return \Farzai\Transport\Contracts\ResponseInterface
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    private function askOpenAi(array $messages, float $temperature = 1.0)
    {
        return $this->openAi->sendCompletion(
            body: [
                'model' => $this->config['model'],
                'messages' => $messages,
                'max_tokens' => $this->config['max_tokens'],
                'temperature' => $temperature,
                'stop' => ['\n'],
            ],
        );
    }

    /**
     * Resolve the query command from the given json.
     *
     * @return string
     */
    private function resolveQueryCommand(array $json)
    {
        return $this->anwserResolver->resolveQueryCommand(
            content: $this->getAssistantMessage($json),
        );
    }

    /**
     * Resolve the assistant message from the given json.
     */
    private function getAssistantMessage(array $json): string
    {
        foreach ($json['choices'] ?? [] as $choice) {
            if ($choice['message']['role'] === 'assistant') {
                return $choice['message']['content'];
            }
        }

        throw new UnexpectedResponse();
    }

    /**
     * Build the tables string.
     */
    private function convertTablesToPrompt(array $tables): string
    {
        $tableNames = array_keys($tables);

        $tableStrings = array_map(function ($table, $columns) {
            $names = array_keys($columns);

            $columnStrings = array_map(function ($column, $type) {
                return "{$column}({$type})";
            }, $names, $columns);

            return $table.': '.implode(',', $columnStrings);
        }, $tableNames, $tables);

        return implode("\n", $tableStrings);
    }
}
