<?php

namespace Farzai\Viola\Commands;

class ShowConfig extends AbstractContextCommand
{
    protected function configure()
    {
        $this->setDescription('Show current database connection and API key.');
    }

    protected function handle(): int
    {
        $apiKey = $this->storage->get('api_key');
        if (! $apiKey) {
            $this->error('API key is not set.');

            return static::FAILURE;
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

        return static::SUCCESS;
    }
}
