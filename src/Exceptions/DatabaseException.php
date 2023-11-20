<?php

namespace Nexa\Exceptions;

use Exception;

class DatabaseException extends  Exception
{

    public  function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}