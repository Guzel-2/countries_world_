<?php

namespace App\Model\Exceptions;

use Throwable;
use Exception;

class CountryNotFoundException extends Exception
{
    public function __construct(string $notFoundCode, Throwable $previous = null)
    {
        $exceptionMessage = "Страна с кодом '{$notFoundCode}' не найдена.";
        parent::__construct(
            message: $exceptionMessage,
            code: ErrorCodes::COUNTRY_NOT_FOUND_ERROR, 
            previous: $previous,
        );
    }
}
