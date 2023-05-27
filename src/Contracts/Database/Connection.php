<?php

namespace Farzai\Viola\Contracts\Database;

interface Connection
{
    /**
     * Perform the given query.
     *
     * @param  string  $query
     * @return array
     */
    public function performQuery(string $query): array;
}