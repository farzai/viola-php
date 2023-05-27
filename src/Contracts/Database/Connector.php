<?php

namespace Farzai\Viola\Contracts\Database;

interface Connector
{
    /**
     * Connect to the database.
     *
     * @return \Farzai\Viola\Contracts\Database\Connection
     */
    public function connect(array $config): Connection;
}
