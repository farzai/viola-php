<?php

use Farzai\Viola\Contracts\StorageRepositoryInterface;
use Farzai\Viola\Storage\DatabaseConnectionRepository;

it('should return the default connection', function () {
    $repository = new DatabaseConnectionRepository(
        \Mockery::mock(StorageRepositoryInterface::class)
            ->shouldReceive('get')
            ->with('database.connections', [])
            ->andReturn([
                'farzai' => [
                    'host' => 'localhost',
                    'port' => 3306,
                    'database' => 'test',
                    'username' => 'root',
                ],
            ])
            ->getMock()
    );

    expect($repository->get('farzai'))->toBe([
        'host' => 'localhost',
        'port' => 3306,
        'database' => 'test',
        'username' => 'root',
    ]);
});

it('should set new connection success', function () {
    $repository = new DatabaseConnectionRepository(
        \Mockery::mock(StorageRepositoryInterface::class)
            ->shouldReceive('get')->once()
            ->with('database.connections', [])
            ->andReturn([])
            ->shouldReceive('set')->once()
            ->with('database.connections', [
                'farzai' => [
                    'host' => 'localhost',
                    'port' => 3306,
                    'database' => 'test',
                    'username' => 'root',
                ],
            ])
            ->getMock()
    );

    $repository->set('farzai', [
        'host' => 'localhost',
        'port' => 3306,
        'database' => 'test',
        'username' => 'root',
    ]);
});

it('should remove connection success', function () {
    $repository = new DatabaseConnectionRepository(
        \Mockery::mock(StorageRepositoryInterface::class)
            ->shouldReceive('get')->once()
            ->with('database.connections', [])
            ->andReturn([
                'farzai' => [
                    'host' => 'localhost',
                    'port' => 3306,
                    'database' => 'test',
                    'username' => 'root',
                ],
            ])
            ->shouldReceive('set')->once()
            ->with('database.connections', [])
            ->shouldReceive('remove')->once()
            ->getMock()
    );

    $repository->remove('farzai');
});

it('should do nothing if connection does not exist when remove connection', function () {
    $repository = new DatabaseConnectionRepository(
        \Mockery::mock(StorageRepositoryInterface::class)
            ->shouldReceive('get')->once()
            ->with('database.connections', [])
            ->andReturn([])
            ->shouldReceive('set')->never()
            ->shouldReceive('remove')->never()
            ->getMock()
    );

    $repository->remove('farzai');
});
