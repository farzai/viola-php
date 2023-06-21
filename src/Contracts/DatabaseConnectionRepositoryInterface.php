<?php

namespace Farzai\Viola\Contracts;

interface DatabaseConnectionRepositoryInterface
{
    /**
     * Get all database connections.
     */
    public function all(): array;

    /**
     * Get a database connection by name.
     */
    public function get(string $name): array;

    /**
     * Add or update a database connection.
     */
    public function set(string $name, array $config): void;

    /**
     * Remove a database connection.
     */
    public function remove(string $name): void;
}
