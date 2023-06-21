<?php

namespace Farzai\Viola\Commands;

use Farzai\Viola\Contracts\DatabaseConnectionRepositoryInterface;
use Farzai\Viola\Contracts\StorageRepositoryInterface;
use Farzai\Viola\Storage\CacheFilesystemStorage;
use Farzai\Viola\Storage\DatabaseConnectionRepository;
use Symfony\Component\Console\Input\InputArgument;

class UseConfig extends Command
{
    protected static $defaultName = 'use';

    private StorageRepositoryInterface $storage;

    private DatabaseConnectionRepositoryInterface $databaseConfig;

    protected function configure()
    {
        $this
            ->setDescription('Switch to a different database connection.')
            ->setHelp("This command allows you to switch to a different database connection.\nYou may run `config:show` to see available connections.")
            ->addArgument('connection', InputArgument::REQUIRED, 'The connection name to use.');

        $this->storage = new CacheFilesystemStorage();
        $this->databaseConfig = new DatabaseConnectionRepository(new CacheFilesystemStorage());
    }

    protected function handle(): int
    {
        $connectionName = $this->input->getArgument('connection');

        $currentConnection = $this->storage->get('database.current');

        $connections = $this->databaseConfig->all();

        $names = array_keys($connections);

        if (! in_array($connectionName, $names)) {
            $this->error("Connection [{$connectionName}] does not exist.");
            $this->error("Available connections: \n".implode("\n", array_map(fn ($name) => " - {$name}", $names)));

            return Command::FAILURE;
        }

        $this->storage->set('database.current', $connectionName);

        if ($currentConnection !== $connectionName) {
            $this->info("Switched to connection [{$connectionName}]");
        } else {
            $this->info("Using connection [{$connectionName}]");
        }

        $connection = $connections[$connectionName];
        foreach (['host', 'port', 'database', 'username'] as $key) {
            if (isset($connection[$key])) {
                $this->info("- {$key}: <comment>{$connection[$key]}</comment>");
            }
        }

        return Command::SUCCESS;
    }
}
