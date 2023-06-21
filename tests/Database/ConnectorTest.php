<?php

use Farzai\Viola\Contracts\Database\ConnectorInterface;
use Farzai\Viola\Database\ConnectorFactory;

it('should create mysql connector', function () {

    $factory = new ConnectorFactory();

    expect($factory->create('mysql'))->toBeInstanceOf(ConnectorInterface::class);
});

it('should create pgsql connector', function () {

    $factory = new ConnectorFactory();

    expect($factory->create('pgsql'))->toBeInstanceOf(ConnectorInterface::class);
});

it('should create sqlsrv connector', function () {

    $factory = new ConnectorFactory();

    expect($factory->create('sqlsrv'))->toBeInstanceOf(ConnectorInterface::class);
});

it('should throw exception when driver is not supported', function () {

    $factory = new ConnectorFactory();

    $factory->create('sqlite');
})->throws(InvalidArgumentException::class);

it('should get all supported drivers', function () {

    $drivers = ConnectorFactory::getAvailableDrivers();

    expect($drivers)->toBeArray();
    expect($drivers)->toContain('mysql');
    expect($drivers)->toContain('pgsql');
    expect($drivers)->toContain('sqlsrv');
});
