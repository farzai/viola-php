<?php

namespace Farzai\Viola;

use Farzai\Viola\Contracts\Database\ConnectorInterface;
use Farzai\Viola\Database\ConnectorFactory;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;

class Builder
{
    /**
     * The database connector.
     */
    private ?ConnectorInterface $connector = null;

    /**
     * Http client
     */
    private ?ClientInterface $client = null;

    /**
     * Logger
     */
    private ?LoggerInterface $logger = null;

    /**
     * The configuration.
     */
    private array $config = [
        // API Key from Open AI
        'api_key' => '',

        'model' => 'gpt-3.5-turbo-16k',

        // Limit the number of results
        'limit' => 50,

        // Database connection
        'database' => [
            // mysql, pgsql, sqlite, sqlsrv
            'driver' => 'mysql',

            'connection' => [
                // See stubs/database.php
            ],
        ],

        'except' => [
            'tables' => [
                'migrations*',
                'failed_jobs',
                'jobs',
                'password_resets',
                'sessions',
                'telescope_*',
            ],

            'columns' => [
                '*password*',
                'remember_token',
            ],
        ],
    ];

    /**
     * Create a new instance.
     */
    public static function make()
    {
        return new static();
    }

    /**
     * Set the database connector.
     */
    public function setConnector(ConnectorInterface $connector)
    {
        $this->connector = $connector;

        return $this;
    }

    /**
     * Set the database driver.
     */
    public function setDatabaseConfig(string $driver, array $config)
    {
        $availableDrivers = ConnectorFactory::getAvailableDrivers();

        if (! in_array($driver, $availableDrivers)) {
            throw new \InvalidArgumentException(
                "Unsupported driver [{$driver}]"
            );
        }

        $this->config['database'] = [
            'driver' => $driver,
            'connection' => $config,
        ];

        return $this;
    }

    /**
     * Set Open AI API key.
     */
    public function setApiKey(string $apiKey)
    {
        $this->config['api_key'] = $apiKey;

        return $this;
    }

    /**
     * Set the http client.
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Set the logger.
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Build the Viola instance.
     */
    public function build(): Viola
    {
        if (! $this->config['api_key']) {
            throw new \InvalidArgumentException(
                'Please set the Open AI API key (api_key).'
            );
        }

        if (! $this->connector) {
            $this->connector = (new ConnectorFactory())->create($this->config['database']['driver']);
        }

        $connection = $this->connector->connect($this->config['database']['connection']);

        return new Viola(
            $this->config,
            $connection,
            $this->client,
            $this->logger,
        );
    }
}
