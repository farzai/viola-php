<?php

namespace Farzai\Viola\Contracts\Database;

interface Connection
{
    /**
     * Perform the given query.
     */
    public function performQuery(string $query): array;
}
