<?php

namespace Farzai\Viola\Database;

use Farzai\Viola\Contracts\Database\ConnectorInterface;

class ConnectorFactory
{
    /**
     * Get all supported drivers.
     */
    public static function getAvailableDrivers(): array
    {
        $config = require __DIR__.'/../../stubs/database.php';

        return array_keys($config['drivers']);
    }

    /**
     * Create a connector instance based on the configuration.
     */
    public function create(string $driver): ConnectorInterface
    {
        return match ($driver) {
            'pgsql' => new PostgreeSql\Connector(),
            'mysql' => new MySQL\Connector(),
            'sqlsrv' => new SqlServer\Connector(),
            default => throw new \InvalidArgumentException(
                "Unsupported driver [{$driver}]"
            )
        };
    }
}
