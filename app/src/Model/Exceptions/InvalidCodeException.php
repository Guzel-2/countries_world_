<?php

namespace App\Model\Exceptions;

use Exception;
use Throwable;

final class InvalidCodeException extends Exception
{
    public function __construct(string $message = 'Недопустимый формат кода страны', Throwable $previous = null)
    {
        parent::__construct($message, ErrorCodes::INVALID_COUNTRY_CODE_ERROR, $previous);  // 1
    }
}
