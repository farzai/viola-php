<?php

namespace Farzai\Viola\Storage;

use Farzai\Viola\Contracts\DatabaseConnectionRepositoryInterface as Contract;
use Farzai\Viola\Contracts\StorageRepositoryInterface;

class DatabaseConnectionRepository implements Contract
{
    private StorageRepositoryInterface $storage;

    /**
     * Create a new database connection repository instance.
     */
    public function __construct(StorageRepositoryInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Get all database connections.
     */
    public function all(): array
    {
        return $this->storage->get('database.connections', []);
    }

    /**
     * Get a database connection by name.
     */
    public function get(string $name): array
    {
        return $this->all()[$name] ?? [];
    }

    /**
     * Add or update a database connection.
     */
    public function set(string $name, array $config): void
    {
        $connections = $this->all();

        $connections[$name] = $config;

        $this->storage->set('database.connections', $connections);
    }

    /**
     * Remove a database connection.
     */
    public function remove(string $name): void
    {
        $connections = $this->all();

        if (! isset($connections[$name])) {
            return;
        }

        unset($connections[$name]);

        $this->storage->set('database.connections', $connections);

        if (count($connections) === 0) {
            $this->storage->remove('database.connections');
        }
    }
}
