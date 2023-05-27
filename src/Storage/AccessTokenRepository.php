<?php

namespace Farzai\Viola\Storage;

use Farzai\Viola\Contracts\TokenRepository as TokenRepositoryContract;
use Farzai\Viola\Contracts\StorageRepository as StorageRepositoryContract;
use Farzai\Viola\Storage\SystemTemporaryStorage;

class AccessTokenRepository implements TokenRepositoryContract
{
    /**
     * @var SystemTemporaryStorage
     */
    private SystemTemporaryStorage $storage;

    /**
     * AccessTokenRepository constructor.
     */
    public function __construct(?StorageRepositoryContract $storage = null)
    {
        $this->storage = $storage ?? new SystemTemporaryStorage('viola');
    }

    /**
     * Get the ChatGPT API token.
     *
     * @return string
     */
    public function getToken(): ?string
    {
        return $this->storage->retrieve('access_token');
    }

    /**
     * Set the ChatGPT API token.
     *
     * @param string $token
     * @return void
     */
    public function setToken(string $token): void
    {
        $this->storage->store('access_token', $token);
    }

    /**
     * Forget API token.
     *
     * @return void
     */
    public function forget(): void
    {
        $this->storage->remove('access_token');
    }
}