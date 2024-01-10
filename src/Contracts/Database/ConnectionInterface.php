<?php

namespace Farzai\Viola\Contracts\Database;

interface ConnectionInterface
{
    /**
     * Get the platform name.
     */
    public function getPlatform(): string;

    /**
     * Perform the given query.
     */
    public function performQuery(string $query): array;

    /**
     * Get all tables in the database.
     *
     * @return array<string>
     */
    public function getTables(): array;
}
