<?php

use Farzai\Viola\Builder;
use Farzai\Viola\Viola;

it('can create a new Viola builder instance', function () {
    expect(Builder::make())->toBeInstanceOf(Builder::class);
    expect(Viola::builder())->toBeInstanceOf(Builder::class);
});

it('can create a new Viola instance', function () {
    $viola = Viola::builder()
        ->setApiKey('secret')
        ->build();

    expect($viola)->toBeInstanceOf(Viola::class);
});

it('can create a new Viola instance with custom client', function () {
    $viola = Viola::builder()
        ->setApiKey('secret')
        ->setClient(\Mockery::mock(\Psr\Http\Client\ClientInterface::class))
        ->build();

    expect($viola)->toBeInstanceOf(Viola::class);
});

it('should throw error if not set api key', function () {
    Viola::builder()->build();
})->throws(\InvalidArgumentException::class, 'Please set the Open AI API key (api_key).');

it('should set custom client and logger success', function () {
    $psrLogger = \Mockery::mock(\Psr\Log\LoggerInterface::class);
    $psrClient = \Mockery::mock(\Psr\Http\Client\ClientInterface::class);

    $viola = Viola::builder()
        ->setApiKey('secret')
        ->setClient($psrClient)
        ->setLogger($psrLogger)
        ->build();

    expect($viola)->toBeInstanceOf(Viola::class);
});

it('should set database config success', function () {
    $driver = 'mysql';

    $viola = Viola::builder()
        ->setApiKey('secret')
        ->setDatabaseConfig($driver, [
            'host' => 'localhost',
        ])
        ->build();

    expect($viola)->toBeInstanceOf(Viola::class);
});

it('should throw if driver not supported', function () {
    $driver = 'sqlite';

    $viola = Viola::builder()
        ->setApiKey('secret')
        ->setDatabaseConfig($driver, [
            'host' => 'localhost',
        ])
        ->build();
})->throws(\InvalidArgumentException::class, 'Unsupported driver [sqlite].');

it('should set connector success', function () {
    $connector = \Mockery::mock(\Farzai\Viola\Contracts\Database\ConnectorInterface::class)
        ->shouldReceive('connect')
        ->with([
            'host' => 'hosting.test',
            'database' => 'database',
        ])
        ->once()
        ->andReturn(\Mockery::mock(\Farzai\Viola\Contracts\Database\ConnectionInterface::class))
        ->getMock();

    $viola = Viola::builder()
        ->setApiKey('secret')
        ->setDatabaseConfig('pgsql', [
            'host' => 'hosting.test',
            'database' => 'database',
        ])
        ->setConnector($connector)
        ->build();

    expect($viola)->toBeInstanceOf(Viola::class);
});
