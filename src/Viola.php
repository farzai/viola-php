<?php

namespace Farzai\Viola;

use Farzai\Viola\Contracts\Actor;
use Farzai\Viola\Contracts\StorageRepository;
use Farzai\Viola\Storage\AccessTokenRepository;
use Farzai\Viola\Storage\SystemTemporaryStorage;
use Farzai\Viola\Contracts\TokenRepository as TokenRepositoryContract;

class Viola
{
    private StorageRepository $storage;

    private AccessTokenRepository $accessTokenRepository;

    private array $actors = [];

    /**
     * Viola constructor.
     */
    public function __construct(?StorageRepository $storage = null)
    {
        $this->storage = $storage ?? new SystemTemporaryStorage('viola');
        $this->accessTokenRepository = new AccessTokenRepository($this->storage);
    }

    
    public function addActor(Actor $actor)
    {
        $this->actors[$actor->getIdentifier()] = $actor;
        return $this;
    }

    public function getTokenStore(): TokenRepositoryContract
    {
        return $this->accessTokenRepository;
    }
}