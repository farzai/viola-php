<?php

namespace Farzai\Viola\Database;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Farzai\Viola\Contracts\Database\ConnectionInterface;
use Farzai\Viola\Contracts\Database\ConnectorInterface;
use SensitiveParameter;

abstract class AbstractConnector implements ConnectorInterface
{
    /**
     * Connect to the database.
     */
    public function connect(
        #[SensitiveParameter] array $config
    ): ConnectionInterface {
        $params = array_merge($this->parseConfig($config), $params ?? []);

        return new DoctrineConnection(DriverManager::getConnection($params));
    }

    /**
     * Parse the database configuration, add the default values.
     */
    protected function parseConfig(array $config): array
    {
        return array_filter([
            'host' => $config['host'] ?? null,
            'port' => $config['port'] ?? null,
            'dbname' => $config['database'] ?? null,
            'user' => $config['username'] ?? null,
            'password' => $config['password'] ?? null,
        ]);
    }

    /**
     * Parse the DSN string.
     */
    protected function parseDsn(string $dsn): array
    {
        return (new DsnParser())->parse($dsn);
    }
}
