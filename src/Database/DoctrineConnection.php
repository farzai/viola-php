<?php

namespace Farzai\Viola\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use Farzai\Viola\Contracts\Database\ConnectionInterface;

final class DoctrineConnection implements ConnectionInterface
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     * DoctrineConnection constructor.
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get the platform name.
     */
    public function getPlatform(): string
    {
        $platform = $this->connection->getDatabasePlatform();

        $name = str_replace('Doctrine\\DBAL\\Platforms\\', '', get_class($platform));

        return str_replace('Platform', '', $name);
    }

    /**
     * Perform the given query.
     */
    public function performQuery(string $query): array
    {
        try {
            $result = $this->connection->executeQuery($query);

            return $result->fetchAllAssociative();
        } catch (\Throwable $th) {
            throw new \Exception("Error Processing Query: \n{$query}\n\n{$th->getMessage()}");
        }
    }

    /**
     * Get all tables in the database.
     *
     * @return array<string>
     */
    public function getTables(): array
    {
        return $this->connection->createSchemaManager()->listTableNames();
    }

    /**
     * Get all columns with types in the given table.
     *
     * @return array<string, string>
     */
    public function getColumns(string $table): array
    {
        $columns = $this->connection->createSchemaManager()->listTableColumns($table);

        $columnsWithType = [];

        foreach ($columns as $column) {
            $type = basename(str_replace('\\', '/', get_class($column->getType())));

            // Remove the "Type" suffix from the type name.
            $type = str_replace('Type', '', $type);
            $type = strtolower($type);

            $columnsWithType[$column->getName()] = $type;
        }

        return $columnsWithType;
    }
}
