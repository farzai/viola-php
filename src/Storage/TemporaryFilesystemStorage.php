<?php

namespace Farzai\Viola\Storage;

use Farzai\Viola\Contracts\StorageRepositoryInterface;

class TemporaryFilesystemStorage implements StorageRepositoryInterface
{
    private string $prefix;

    public function __construct(string $prefix = 'viola_storage')
    {
        $this->prefix = md5($prefix);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $filename = $this->getFilename($key);

        if (file_exists($filename)) {
            return unserialize(file_get_contents($filename));
        }

        return $default;
    }

    public function set(string $key, mixed $value): void
    {
        $filename = $this->getFilename($key);

        file_put_contents($filename, serialize($value));
    }

    public function has(string $key): bool
    {
        return file_exists($this->getFilename($key));
    }

    public function remove(string $key): void
    {
        $filename = $this->getFilename($key);

        if (file_exists($filename)) {
            unlink($filename);
        }
    }

    private function getFilename(string $key): string
    {
        return sys_get_temp_dir().DIRECTORY_SEPARATOR.$this->prefix.'_'.md5($key);
    }
}
