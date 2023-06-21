<?php

namespace Farzai\Viola\Exceptions;

use Exception;

class SqlCommandUnsafe extends Exception
{
    /**
     * @return static
     */
    public static function create(string $query): self
    {
        return new static("Query command is unsafe: \n`{$query}`");
    }
}
