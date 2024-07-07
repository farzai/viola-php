<?php

use Farzai\Viola\Storage\TemporaryFilesystemStorage;

beforeEach(function () {
    // Clear the temporary storage before each test.
    $storage = new TemporaryFilesystemStorage('test');

    $storage->remove('foo');
});

it('should return the value of the given key', function () {
    $storage = new TemporaryFilesystemStorage('test');

    $storage->set('foo', 'bar');

    expect($storage->get('foo'))->toBe('bar');
});

it('should return the default value if the key does not exist', function () {
    $storage = new TemporaryFilesystemStorage('test');

    expect($storage->get('foo', 'bar'))->toBe('bar');
});

it('should return true if the key exists', function () {
    $storage = new TemporaryFilesystemStorage('test');

    $storage->set('foo', 'bar');

    expect($storage->has('foo'))->toBeTrue();
});

it('should return false if the key does not exist', function () {
    $storage = new TemporaryFilesystemStorage('test');

    expect($storage->has('foo'))->toBeFalse();
});

it('should remove the given key', function () {
    $storage = new TemporaryFilesystemStorage('test');

    $storage->set('foo', 'bar');
    $storage->remove('foo');

    expect($storage->has('foo'))->toBeFalse();
});
