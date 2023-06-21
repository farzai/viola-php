<?php

namespace Farzai\Viola\Commands;

use Farzai\Viola\Contracts\DatabaseConnectionRepositoryInterface;
use Farzai\Viola\Contracts\StorageRepositoryInterface;
use Farzai\Viola\Storage\CacheFilesystemStorage;
use Farzai\Viola\Storage\DatabaseConnectionRepository;

class ShowConfig extends Command
{
    protected static $defaultName = 'config:show';

    private StorageRepositoryInterface $storage;

    private DatabaseConnectionRepositoryInterface $databaseConfig;

    protected function configure()
    {
        $this
            ->setDescription('Show current database connection and API key.');

        $this->storage = new CacheFilesystemStorage();
        $this->databaseConfig = new DatabaseConnectionRepository(new CacheFilesystemStorage());
    }

    protected function handle(): int
    {
        $apiKey = $this->storage->get('api_key');
        if (! $apiKey) {
            $this->error('API key is not set.');

            return Command::FAILURE;
        }

        $this->info("API Key: <comment>{$apiKey}</comment>");

        $connections = $this->databaseConfig->all();

        $currentConnection = $this->storage->get('database.current');

        if (count($connections) > 0) {
            $this->info('Database Connections:');

            foreach ($connections as $name => $value) {
                $this->info(" - {$name} <comment>[{$value['driver']}]</comment>".($currentConnection === $name ? ' <comment>(*)</comment>' : ''));
            }
        } else {
            $this->info('Database Connection: <comment>Not set</comment>');
        }

        return Command::SUCCESS;
    }
}
