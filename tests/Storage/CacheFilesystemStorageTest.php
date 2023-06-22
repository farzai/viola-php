<?php

use Farzai\Viola\Storage\CacheFilesystemStorage;

beforeEach(function () {
    // Clear the cache before each test.
    $storage = new CacheFilesystemStorage('test');

    $storage->remove('foo');
});

it('should return the value of the given key', function () {
    $storage = new CacheFilesystemStorage('test');

    $storage->set('foo', 'bar');

    expect($storage->get('foo'))->toBe('bar');
});

it('should return the default value if the key does not exist', function () {
    $storage = new CacheFilesystemStorage('test');

    expect($storage->get('foo', 'bar'))->toBe('bar');
});

it('should return true if the key exists', function () {
    $storage = new CacheFilesystemStorage('test');

    $storage->set('foo', 'bar');

    expect($storage->has('foo'))->toBeTrue();
});

it('should return false if the key does not exist', function () {
    $storage = new CacheFilesystemStorage('test');

    expect($storage->has('foo'))->toBeFalse();
});
