<?php

namespace App\Model\Exceptions;

use Exception;

class InvalidCountryException extends Exception
{
    private array $errors;

    public function __construct(string $message, array $errors = [], int $errorCode = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $errorCode, $previous);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
