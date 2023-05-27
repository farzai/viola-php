<?php

namespace Farzai\Viola\Contracts;

interface StorageRepository
{
    /**
     * Store the given data.
     *
     * @param  string  $key
     * @param  mixed  $data
     * @return void
     */
    public function store(string $key, $data): void;

    /**
     * Retrieve the data from the given key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function retrieve(string $key);

    /**
     * Check if the given key exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function exists(string $key): bool;

    /**
     * Remove the given key.
     *
     * @param  string  $key
     * @return void
     */
    public function remove(string $key): void;
}