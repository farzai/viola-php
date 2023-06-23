<?php

namespace Farzai\Viola\Commands;

use Farzai\Viola\Viola;
use Psr\Log\NullLogger;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Logger\ConsoleLogger;

class AskQuestion extends AbstractContextCommand
{
    protected function configure()
    {
        $this
            ->setDescription('Ask a question to the ChatGPT')
            ->addArgument('question', InputArgument::REQUIRED, 'The question to ask')
            ->setHelp("This command allows you to ask a question to the ChatGPT.\nYou may run `config:show` to see available connections for confirmation.")
            ->addOption('only-result', null, null, 'Do not display the answer')
            ->addOption('debug', 'd', null, 'Display the debug information');
    }

    protected function handle(): int
    {
        $question = $this->input->getArgument('question');

        $this->ensureDatabaseIsConfigured();

        $config = $this->getDatabaseConfig();

        $viola = Viola::builder()
            ->setDatabaseConfig($config['driver'], $config)
            ->setApiKey($this->storage->get('api_key'))
            ->setLogger(
                $this->input->getOption('debug')
                    ? new ConsoleLogger($this->output)
                    : new NullLogger()
            )
            ->build();

        $response = $viola
            ->onlyResults((bool) $this->input->getOption('only-result'))
            ->ask($question);

        $this->info($response->getAnswer());

        if (count($response->getResults())) {
            $this->displayAsTable($response->getResults());
        }

        return static::SUCCESS;
    }

    private function ensureDatabaseIsConfigured()
    {
        $connections = $this->databaseConfig->all();
        if (count($connections) === 0) {
            $this->error("Database connection is not set.\nPlease run `viola config` to set the database connection.");
            exit;
        }

        $currentName = $this->storage->get('database.current');

        if (! $currentName) {
            $currentName = $this->choice('Which connection do you want to use?', array_keys($connections), 0);

            // Set the current connection.
            $this->storage->set('database.current', $currentName);
        }
    }

    private function getDatabaseConfig(): array
    {
        $connectionName = $this->storage->get('database.current');

        return $this->databaseConfig->get($connectionName);
    }
}
