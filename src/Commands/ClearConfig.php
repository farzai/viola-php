<?php

namespace Farzai\Viola\Commands;

use Farzai\Viola\Contracts\StorageRepositoryInterface;
use Farzai\Viola\Storage\CacheFilesystemStorage;
use Symfony\Component\Console\Input\InputArgument;

class ClearConfig extends Command
{
    protected static $defaultName = 'config:clear';

    private StorageRepositoryInterface $storage;

    /**
     * Create a new instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->storage = new CacheFilesystemStorage();
    }

    protected function configure()
    {
        $this
            ->setDescription('Clear database connection.')
            ->setHelp('Type `config:clear all` to clear all connections.')
            ->addArgument('connection', InputArgument::REQUIRED, 'The database connection name to clear.');
    }

    protected function handle(): int
    {
        $connection = $this->input->getArgument('connection');

        if (strtolower($connection) === 'all') {
            if ($this->confirm('Are you sure you want to clear all connections?', false)) {
                $connections = $this->storage->get('database.connections', []);

                foreach ($connections as $name => $value) {
                    $this->storage->remove("database.connections.{$name}");

                    $this->info("Connection {$name} cleared.");
                }

                $this->storage->remove('database_connections');

                $this->info('All connections cleared.');

                return Command::SUCCESS;
            }

            $this->info('Cancelled.');

            return Command::SUCCESS;
        }

        if (! $this->storage->has("database.connections.{$connection}")) {
            $this->error("Connection {$connection} does not exist.");

            return Command::FAILURE;
        }

        if ($this->confirm("Are you sure you want to clear connection {$connection}?", false)) {
            $this->storage->remove("database.connections.{$connection}");

            $this->info("Connection {$connection} cleared.");

            return Command::SUCCESS;
        }

        return Command::SUCCESS;
    }
}
