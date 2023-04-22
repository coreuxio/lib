<?php

namespace Coreux\lib\API\Exceptions;

use Exception;
use Throwable;

class APIValidationException extends Exception
{
    public array $errors = [];
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null,array $errors=null)
    {
        if(is_array($errors)){
            $this->errors = $errors;
        }
        parent::__construct($message, $code, $previous);
    }
}
