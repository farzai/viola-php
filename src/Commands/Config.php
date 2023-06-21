<?php

namespace Farzai\Viola\Commands;

use Farzai\Viola\Contracts\DatabaseConnectionRepositoryInterface;
use Farzai\Viola\Contracts\StorageRepositoryInterface;
use Farzai\Viola\Database\ConnectorFactory;
use Farzai\Viola\Storage\CacheFilesystemStorage;
use Farzai\Viola\Storage\DatabaseConnectionRepository;

class Config extends Command
{
    protected static $defaultName = 'config';

    private StorageRepositoryInterface $storage;

    private DatabaseConnectionRepositoryInterface $databaseConfig;

    protected function configure()
    {
        $this->setDescription('Config API key and database connection.');

        $this->storage = new CacheFilesystemStorage();
        $this->databaseConfig = new DatabaseConnectionRepository(new CacheFilesystemStorage());
    }

    protected function handle(): int
    {
        $this->info('Setting up Viola...');

        $apiKey = $this->storage->get('api_key');

        $question = 'What is your OpenAI key? ';
        if ($apiKey) {
            $question .= '<comment>[Leave empty to keep current value]</comment> ';
        }

        if ($newVal = $this->askWithHiddenInput($question, $apiKey)) {
            $this->storage->set('api_key', $newVal);
        } else {
            $this->error('API key is required.');

            return Command::FAILURE;
        }

        $this->info('Setting up database connection...');

        // Ask for connection name
        $connectionName = $this->ask('What is your database connection name? ');
        if (! $connectionName || empty($connectionName)) {
            $this->error('Database connection name is required.');

            return Command::FAILURE;
        }

        $connections = $this->databaseConfig->all();

        if (count($connections) > 0) {
            $connectionNames = array_keys($connections);

            if (in_array($connectionName, $connectionNames)) {
                if (! $this->confirm("Connection {$connectionName} already exists. Do you want to continue edit it?")) {
                    $this->info('Cancelled.');

                    return Command::SUCCESS;
                }
            }
        }

        $config = $this->databaseConfig->get($connectionName);
        $driver = $config['driver'] ?? null;

        $question = 'What is your database driver?';
        if ($driver) {
            $driver = $this->choice($question."<comment>[{$driver}]</comment>: ", ConnectorFactory::getAvailableDrivers(), $driver);
        } else {
            $driver = $this->choice($question.': ', ConnectorFactory::getAvailableDrivers());
        }

        $stubConfig = require __DIR__.'/../../stubs/database.php';

        if (! isset($stubConfig['drivers'][$driver])) {
            $this->error('Only support '.implode(', ', array_keys($stubConfig['drivers'])).' driver.');

            return Command::FAILURE;
        }

        $stubConfig = $stubConfig['drivers'][$driver];
        foreach ($stubConfig as $key => $value) {
            if (in_array($key, ['driver', 'options'])) {
                continue;
            }

            $value = $config[$key] ?? $value ?: null;
            $question = "What is your {$key}? ";

            // Hide sensitive information
            if (in_array($key, ['password'])) {
                $question .= ($value ? '<comment>[hidden]</comment> ' : '');

                $stubConfig[$key] = $this->askWithHiddenInput($question, $value);
            } else {
                $question .= ($value ? "<comment>[{$value}]</comment> " : '');

                $stubConfig[$key] = $this->ask($question, $value);
            }
        }

        $this->databaseConfig->set($connectionName, array_merge($config, $stubConfig, [
            'driver' => $driver,
        ]));

        $this->info('Setup complete!');

        if ($this->confirm('Do you want to test the connection?')) {
            $this->info('Testing connection...');

            $connection = (new ConnectorFactory())
                ->create($driver)
                ->connect($stubConfig);

            try {
                $platform = $connection->getPlatform();

                $this->info('Connection successful!, Platform: '.$platform);

                return Command::SUCCESS;
            } catch (\Exception $e) {
                $this->error('Connection failed!, '.$e->getMessage());
            }

            if (! $this->confirm('Do you want to remove the connection?')) {
                $this->databaseConfig->remove($connectionName);

                if ($this->storage->get('database.current') === $connectionName) {
                    $this->storage->remove('database.current');
                }

                $this->info('Connection removed.');
            }
        }

        return Command::SUCCESS;
    }
}
