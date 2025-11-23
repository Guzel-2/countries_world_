<?php

namespace App\Model\Exceptions;

use Throwable;
use Exception;

class DuplicatedCountryException extends Exception
{
    public function __construct(string $duplicatedName, string $field = 'shortName', Throwable $previous = null)
    {
        $exceptionMessage = "Название страны '{$duplicatedName}' ({$field}) уже существует.";
        parent::__construct(
            message: $exceptionMessage,
            code: ErrorCodes::DUPLICATED_COUNTRY_ERROR,  // 5
            previous: $previous,
        );
    }
}
