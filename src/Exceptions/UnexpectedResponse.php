<?php

namespace Farzai\Viola\Exceptions;

use Exception;

class UnexpectedResponse extends Exception
{
    public function __construct($message = 'Unexpected response from the server', $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
