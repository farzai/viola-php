<?php

namespace Farzai\Viola\Contracts;

interface StorageRepositoryInterface
{
    /**
     * Get the value of the given key.
     *
     * @param  mixed  $default
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Set the value of the given key.
     */
    public function set(string $key, mixed $value): void;

    /**
     * Determine if the given key exists.
     */
    public function has(string $key): bool;

    /**
     * Remove the given key.
     */
    public function remove(string $key): void;
}
