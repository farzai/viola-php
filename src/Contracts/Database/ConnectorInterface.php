<?php

namespace Farzai\Viola\Contracts\Database;

use SensitiveParameter;

interface ConnectorInterface
{
    /**
     * Connect to the database.
     */
    public function connect(
        #[SensitiveParameter] array $config
    ): ConnectionInterface;
}
