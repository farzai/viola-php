<?php

namespace Farzai\Viola\Commands;

use Symfony\Component\Console\Input\InputArgument;

class ClearConfig extends AbstractContextCommand
{
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

                return static::SUCCESS;
            }

            $this->info('Cancelled.');

            return static::SUCCESS;
        }

        if (! $this->storage->has("database.connections.{$connection}")) {
            $this->error("Connection {$connection} does not exist.");

            return static::FAILURE;
        }

        if ($this->confirm("Are you sure you want to clear connection {$connection}?", false)) {
            $this->storage->remove("database.connections.{$connection}");

            $this->info("Connection {$connection} cleared.");

            return static::SUCCESS;
        }

        return static::SUCCESS;
    }
}
