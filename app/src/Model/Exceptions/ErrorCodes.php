<?php

namespace App\Model\Exceptions;

class ErrorCodes
{
    const INVALID_COUNTRY_CODE_ERROR = 1;  // Для невалидного кода (InvalidCodeException)
    const COUNTRY_NOT_FOUND_ERROR = 2;     // Для 404 (CountryNotFoundException)
    const DUPLICATED_CODE_ERROR = 3;       // Для дубликатов кодов (DuplicatedCodeException)
    const INVALID_COUNTRY_ERROR = 4;       // Для невалидных данных (InvalidCountryException, incl. "codes cannot be changed")
    const DUPLICATED_COUNTRY_ERROR = 5;    // Для дубликатов названий (DuplicatedCountryException)
}
