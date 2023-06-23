<?php

namespace Farzai\Viola\Commands;

use Farzai\Viola\Contracts\DatabaseConnectionRepositoryInterface;
use Farzai\Viola\Contracts\StorageRepositoryInterface;

abstract class AbstractContextCommand extends AbstractCommand
{
    public function __construct(
        string $name,
        protected StorageRepositoryInterface $storage,
        protected DatabaseConnectionRepositoryInterface $databaseConfig
    ) {
        parent::__construct($name);
    }
}
