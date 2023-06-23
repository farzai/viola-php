<?php

namespace Farzai\Viola\Commands;

use Symfony\Component\Console\Input\InputArgument;

class UseConfig extends AbstractContextCommand
{
    protected function configure()
    {
        $this
            ->setDescription('Switch to a different database connection.')
            ->setHelp("This command allows you to switch to a different database connection.\nYou may run `config:show` to see available connections.")
            ->addArgument('connection', InputArgument::REQUIRED, 'The connection name to use.');
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

            return static::FAILURE;
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

        return static::SUCCESS;
    }
}
