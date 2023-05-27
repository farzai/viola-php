<?php

namespace Farzai\Viola\Storage;

use Farzai\Viola\Contracts\StorageRepository;

class SystemTemporaryStorage implements StorageRepository
{
    /**
     * The path to the storage.
     */
    private string $path;

    /**
     * Create a new instance.
     */
    public function __construct(string $prefix = '')
    {
        $hash = base64_encode(dirname(dirname(__DIR__)));

        $this->path = sys_get_temp_dir().DIRECTORY_SEPARATOR.$prefix.$hash;
    }

    
    /**
     * Store the given data.
     *
     * @param  string  $key
     * @param  mixed  $data
     * @return void
     */
    public function store(string $key, $data): void
    {
        if (is_array($data) || is_object($data)) {
            $data = serialize($data);
        }
        
        $this->write($key, $data);
    }

    /**
     * Retrieve the data from the given key.
     *
     * @param  string  $key
     * @return string|null
     */
    public function retrieve(string $key)
    {
        $value = $this->read($key);

        if ($value === false) {
            return null;
        }

        // Try to unserialize the value.
        $unserialized = @unserialize($value);

        if ($unserialized !== false) {
            return $unserialized;
        }

        return $value;
    }

    /**
     * Check if the given key exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function exists(string $key): bool
    {
        return $this->read($key) !== false;
    }

    /**
     * Remove the given key.
     *
     * @param  string  $key
     * @return void
     */
    public function remove(string $key): void
    {
        $this->write($key, null);
    }


    /**
     * Read the value of the given key.
     *
     * @return string|false
     */
    private function read(string $key)
    {
        $path = $this->path.DIRECTORY_SEPARATOR.$key;

        if (! file_exists($path)) {
            return false;
        }

        return @file_get_contents($path);
    }


    /**
     * Write the value of the given key.
     *
     * @return void
     */
    private function write(string $key, ?string $value)
    {
        $path = $this->path.DIRECTORY_SEPARATOR.$key;

        if ($value === null) {
            if (file_exists($path)) {
                unlink($path);
            }

            return;
        }

        if (! file_exists($this->path)) {
            mkdir($this->path, 0777, true);
        }

        file_put_contents($path, $value);
    }


    /**
     * Delete the given directory.
     *
     * @return void
     */
    private function deleteDirectory(string $path)
    {
        if (! file_exists($path)) {
            return;
        }

        $files = array_diff(scandir($path), ['.', '..']);

        foreach ($files as $file) {
            $file = $path.DIRECTORY_SEPARATOR.$file;

            if (is_dir($file)) {
                $this->deleteDirectory($file);
            } else {
                unlink($file);
            }
        }
    }
}