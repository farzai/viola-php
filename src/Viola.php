<?php

namespace Farzai\Viola;

use Exception;
use Farzai\Transport\Response;
use Farzai\Transport\TransportBuilder;
use Farzai\Viola\Contracts\Database\ConnectionInterface;
use Farzai\Viola\Contracts\ViolaResponseInterface;
use Farzai\Viola\Exceptions\SqlCommandUnsafe;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;

class Viola
{
    /**
     * The configuration.
     */
    protected array $config;

    /**
     * The database connection.
     */
    protected ConnectionInterface $database;

    /**
     * Cache the tables and columns.
     */
    private $cache = [
        'platform' => null,
        'tables' => null,
        'columns' => null,
    ];

    /**
     * The transport.
     */
    private ClientInterface $transport;

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
        $this->config = $config;
        $this->database = $database;

        $builder = TransportBuilder::make();
        if ($client) {
            $builder->setClient($client);
        }
        if ($logger) {
            $builder->setLogger($logger);
        }
        $this->transport = $builder->build();
    }

    /**
     * Ask a question.
     */
    public function ask(string $question): ViolaResponseInterface
    {
        $messages[] = [
            'role' => 'system',
            'content' => $this->buildSystemPrompt($question),
        ];

        $response = $this->askOpenAi($messages);

        $queryCommand = $this->resolveQueryCommand($response->json());

        $this->ensureQueryCommandIsSafe($queryCommand);

        $results = $this->database->performQuery($queryCommand);

        if (count($results) === 0) {
            return new ViolaResponse($response->json(), $queryCommand, $results);
        }

        if (count($results) < 5) {
            array_push($messages, [
                'role' => 'assistant',
                'content' => "SQLQuery: \"{$queryCommand}\"",
            ], [
                'role' => 'user',
                'content' => $this->buildPrompt('summary', [
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
            $tables = $this->filterMatchingTables($question, $tables);
        }

        return $this->buildPrompt('query', [
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
                throw SqlCommandUnsafe::create($query);
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
                'content' => $this->buildPrompt('tables', [
                    'tables' => $this->convertTablesToPrompt($tables),
                    'question' => $question,
                ]),
            ],
        ]);

        $json = $response->json();

        $names = explode(',', $this->resolveAssistantMessage($json));
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
        $psrRequest = $this->createRequest(
            headers: [
                'Authorization' => 'Bearer '.$this->config['api_key'],
                'Content-Type' => 'application/json',
            ],
            body: [
                'model' => $this->config['model'],
                'messages' => $messages,
                'max_tokens' => 100,
                'temperature' => $temperature,
                'stop' => ['\n'],
            ],
        );

        $psrResponse = $this->transport->sendRequest($psrRequest);

        return new Response($psrRequest, $psrResponse);
    }

    /**
     * Create the request.
     */
    private function createRequest(array $headers, array $body): RequestInterface
    {
        return new Request('POST', 'https://api.openai.com/v1/chat/completions', $headers, json_encode($body));
    }

    /**
     * Resolve the query command from the given json.
     *
     * @return string
     */
    private function resolveQueryCommand(array $json)
    {
        $content = $this->resolveAssistantMessage($json);

        // Trim the content.
        $content = trim($content);

        // Remove the new line.
        $content = implode(' ', array_map(fn ($line) => trim($line), explode("\n", $content)));

        if (preg_match('/SQLQuery: "(.*?)"/im', $content, $matches)) {
            return rtrim($matches[1], ';');
        }

        throw new Exception('Unexpected response from the server');
    }

    /**
     * Resolve the assistant message from the given json.
     */
    private function resolveAssistantMessage(array $json): string
    {
        if (! isset($json['choices'])) {
            throw new Exception('Unexpected response from the server');
        }

        foreach ($json['choices'] as $choice) {
            if ($choice['message']['role'] === 'assistant') {
                return $choice['message']['content'];
            }
        }

        throw new Exception('Unexpected response from the server');
    }

    /**
     * Get the prompt content.
     */
    private function buildPrompt(string $name, array $bindings = []): string
    {
        $content = file_get_contents(__DIR__."/../stubs/prompts/{$name}.txt");

        foreach ($bindings as $key => $value) {
            $content = str_replace(":{$key}:", (string) $value, $content);
        }

        return $content;
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
