<?php

use Farzai\Viola\Contracts\Database\ConnectionInterface;
use Farzai\Viola\Database\DoctrineConnection;

it('should return the correct platform', function () {
    $connection = new DoctrineConnection(
        \Mockery::mock(\Doctrine\DBAL\Connection::class)
            ->shouldReceive('getDatabasePlatform')
            ->andReturn(new \Doctrine\DBAL\Platforms\MySQL80Platform())
            ->getMock()
    );

    expect($connection)->toBeInstanceOf(ConnectionInterface::class);

    expect($connection->getPlatform())->toBe('MySQL80');
});

it('should return the correct tables', function () {
    $connection = new DoctrineConnection(
        \Mockery::mock(\Doctrine\DBAL\Connection::class)
            ->shouldReceive('createSchemaManager')
            ->andReturn(
                \Mockery::mock(\Doctrine\DBAL\Schema\AbstractSchemaManager::class)
                    ->shouldReceive('listTableNames')->once()
                    ->andReturn([
                        'users',
                        'posts',
                    ])
                    ->getMock()
            )
            ->getMock()
    );

    expect($connection->getTables())->toBe([
        'users',
        'posts',
    ]);
});

it('should return the correct columns', function () {
    $connection = new DoctrineConnection(
        \Mockery::mock(\Doctrine\DBAL\Connection::class)
            ->shouldReceive('executeQuery')
            ->andReturn(
                \Mockery::mock(\Doctrine\DBAL\Result::class)
                    ->shouldReceive('fetchAllAssociative')->once()
                    ->andReturn([
                        ['id' => 1, 'name' => 'Farzad'],
                        ['id' => 2, 'name' => 'John'],
                    ])
                    ->getMock()
            )
            ->getMock()
    );

    expect($connection->performQuery('SELECT * FROM users'))->toBe([
        ['id' => 1, 'name' => 'Farzad'],
        ['id' => 2, 'name' => 'John'],
    ]);
});

it('should throw an exception when the query is invalid', function () {
    $connection = new DoctrineConnection(
        \Mockery::mock(\Doctrine\DBAL\Connection::class)
            ->shouldReceive('executeQuery')
            ->andThrow(new \Exception('Invalid Query'))
            ->getMock()
    );

    $connection->performQuery('SELECT * FROM users');
})->throws(\Exception::class, "Error Processing Query: \nSELECT * FROM users\n\nInvalid Query");

it('should get the correct columns', function () {
    $connection = new DoctrineConnection(
        \Mockery::mock(\Doctrine\DBAL\Connection::class)
            ->shouldReceive('createSchemaManager')
            ->andReturn(
                \Mockery::mock(\Doctrine\DBAL\Schema\AbstractSchemaManager::class)
                    ->shouldReceive('listTableColumns')
                    ->andReturn([
                        \Mockery::mock(\Doctrine\DBAL\Schema\Column::class)
                            ->shouldReceive('getName')->once()
                            ->andReturn('id')
                            ->shouldReceive('getType')->once()
                            ->andReturn(
                                \Mockery::mock(\Doctrine\DBAL\Types\IntegerType::class)
                                    ->shouldReceive('getName')->once()
                                    ->andReturn('integer')
                                    ->getMock()
                            )
                            ->getMock(),

                        \Mockery::mock(\Doctrine\DBAL\Schema\Column::class)
                            ->shouldReceive('getName')->once()
                            ->andReturn('name')
                            ->shouldReceive('getType')->once()
                            ->andReturn(
                                \Mockery::mock(\Doctrine\DBAL\Types\StringType::class)
                                    ->shouldReceive('getName')->once()
                                    ->andReturn('string')
                                    ->getMock()
                            )
                            ->getMock(),
                    ])
                    ->getMock()
            )
            ->getMock()
    );

    expect($connection->getColumns('users'))->toBe([
        'id' => 'integer',
        'name' => 'string',
    ]);
});
