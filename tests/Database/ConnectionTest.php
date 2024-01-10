<?php

use Farzai\Viola\Contracts\Database\ConnectionInterface;
use Farzai\Viola\Database\DoctrineConnection;

it('should return the correct platform', function () {
    $mysql = new \Doctrine\DBAL\Platforms\MySQL80Platform();
    $mysqlConnection = $this->createMock(\Doctrine\DBAL\Connection::class);
    $mysqlConnection->expects($this->once())
        ->method('getDatabasePlatform')
        ->willReturn($mysql);

    $connection = new DoctrineConnection($mysqlConnection);

    expect($connection)->toBeInstanceOf(ConnectionInterface::class);

    expect($connection->getPlatform())->toBe('MySQL80');
});

it('should return the correct tables', function () {
    $schemaManager = $this->createMock(\Doctrine\DBAL\Schema\AbstractSchemaManager::class);
    $schemaManager->expects($this->once())
        ->method('listTableNames')
        ->willReturn([
            'users',
            'posts',
        ]);

    $mysqlConnection = $this->createMock(\Doctrine\DBAL\Connection::class);
    $mysqlConnection->expects($this->once())
        ->method('createSchemaManager')
        ->willReturn($schemaManager);

    $connection = new DoctrineConnection($mysqlConnection);

    expect($connection->getTables())->toBe([
        'users',
        'posts',
    ]);
});

it('should throw an exception when the query is invalid', function () {
    $mysqlConnection = $this->createMock(\Doctrine\DBAL\Connection::class);
    $mysqlConnection->expects($this->once())
        ->method('executeQuery')
        ->willThrowException(new \Exception('Invalid Query'));

    $connection = new DoctrineConnection($mysqlConnection);

    $connection->performQuery('SELECT * FROM users');
})->throws(\Exception::class, "Error Processing Query: \nSELECT * FROM users\n\nInvalid Query");

it('should get the correct columns from table successfully', function () {
    $schemaManager = $this->createMock(\Doctrine\DBAL\Schema\AbstractSchemaManager::class);
    $schemaManager->expects($this->once())
        ->method('listTableColumns')
        ->willReturn([
            new \Doctrine\DBAL\Schema\Column('id', new \Doctrine\DBAL\Types\IntegerType()),
            new \Doctrine\DBAL\Schema\Column('name', new \Doctrine\DBAL\Types\StringType()),
            new \Doctrine\DBAL\Schema\Column('email', new \Doctrine\DBAL\Types\StringType()),
        ]);

    $mysqlConnection = $this->createMock(\Doctrine\DBAL\Connection::class);
    $mysqlConnection->expects($this->once())
        ->method('createSchemaManager')
        ->willReturn($schemaManager);

    $connection = new DoctrineConnection($mysqlConnection);

    expect($connection->getColumns('users'))->toBe([
        'id' => 'integer',
        'name' => 'string',
        'email' => 'string',
    ]);
});
