<?php

namespace Farzai\Viola\Exceptions;

class UnexpectedAnswer extends \Exception
{
    public function __construct($message = 'Unexpected answer from the server')
    {
        parent::__construct($message);
    }
}
