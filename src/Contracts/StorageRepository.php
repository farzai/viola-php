<?php

namespace Farzai\Viola\Contracts;

interface StorageRepository
{
    /**
     * Store the given data.
     *
     * @param  mixed  $data
     */
    public function store(string $key, $data): void;

    /**
     * Retrieve the data from the given key.
     *
     * @return mixed
     */
    public function retrieve(string $key);

    /**
     * Check if the given key exists.
     */
    public function exists(string $key): bool;

    /**
     * Remove the given key.
     */
    public function remove(string $key): void;
}
