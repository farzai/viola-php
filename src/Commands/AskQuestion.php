<?php

namespace Farzai\Viola\Commands;

use Farzai\Viola\Storage\CacheFilesystemStorage;
use Farzai\Viola\Storage\DatabaseConnectionRepository;
use Farzai\Viola\Viola;
use Symfony\Component\Console\Input\InputArgument;

class AskQuestion extends Command
{
    protected static $defaultName = 'ask';

    /**
     * @var \Farzai\Viola\Contracts\StorageRepositoryInterface
     */
    private $storage;

    /**
     * @var \Farzai\Viola\Contracts\DatabaseConnectionRepositoryInterface
     */
    private $databaseConfig;

    protected function configure()
    {
        $this
            ->setDescription('Ask a question to the ChatGPT')
            ->addArgument('question', InputArgument::REQUIRED, 'The question to ask')
            ->setHelp("This command allows you to ask a question to the ChatGPT.\nYou may run `config:show` to see available connections for confirmation.");

        $this->storage = new CacheFilesystemStorage();
        $this->databaseConfig = new DatabaseConnectionRepository(new CacheFilesystemStorage());
    }

    protected function handle(): int
    {
        $question = $this->input->getArgument('question');

        $this->ensureDatabaseIsConfigured();

        $config = $this->getDatabaseConfig();

        $viola = Viola::builder()
            ->setDatabaseConfig($config['driver'], $config)
            ->setApiKey($this->storage->get('api_key'))
            ->build();

        $response = $viola->ask($question);

        $this->info($response->getAnswer());

        $this->displayAsTable($response->getResults());

        return Command::SUCCESS;
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
