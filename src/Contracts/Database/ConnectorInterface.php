<?php

namespace Farzai\Viola\Contracts\Database;

use SensitiveParameter;

interface ConnectorInterface
{
    /**
     * Connect to the database.
     *
     * @return \Farzai\Viola\Contracts\Database\ConnectionInterface
     */
    public function connect(
        #[SensitiveParameter] array $config
    ): ConnectionInterface;
}
