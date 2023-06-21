<?php

namespace Farzai\Viola\Storage;

use Farzai\Viola\Contracts\StorageRepositoryInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class CacheFilesystemStorage implements StorageRepositoryInterface
{
    /**
     * @var \Symfony\Component\Cache\Adapter\FilesystemAdapter
     */
    private $cache;

    /**
     * Create a new filesystem storage instance.
     */
    public function __construct($prefix = '')
    {
        $this->cache = new FilesystemAdapter("viola_{$prefix}");
    }

    /**
     * Get the value of the given key.
     *
     * @param  mixed  $default
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if (! $this->has($key)) {
            return $default;
        }

        return $this->cache->getItem($key)->get();
    }

    /**
     * Set the value of the given key.
     */
    public function set(string $key, mixed $value): void
    {
        $item = $this->cache->getItem($key);

        $item->set($value);

        $this->cache->save($item);
    }

    /**
     * Determine if the given key exists.
     */
    public function has(string $key): bool
    {
        return $this->cache->hasItem($key);
    }

    /**
     * Remove the given key.
     */
    public function remove(string $key): void
    {
        $this->cache->deleteItem($key);
    }
}
