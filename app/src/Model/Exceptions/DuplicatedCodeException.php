<?php

namespace App\Model\Exceptions;

use Throwable;
use Exception;

class DuplicatedCodeException extends Exception
{
    public function __construct(string $duplicatedCode, string $field = 'isoAlpha2', Throwable $previous = null)
    {
        $exceptionMessage = "Код страны '{$duplicatedCode}' ({$field}) уже существует.";
        parent::__construct(
            message: $exceptionMessage,
            code: ErrorCodes::DUPLICATED_CODE_ERROR,  // 3
            previous: $previous,
        );
    }
}
