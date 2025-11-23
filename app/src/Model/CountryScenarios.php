<?php

namespace App\Model;

use App\Model\CountryRepository;
use App\Model\Exceptions\CountryNotFoundException;
use App\Model\Exceptions\InvalidCodeException;
use App\Model\Exceptions\DuplicatedCodeException;
use App\Model\Exceptions\InvalidCountryException;
use App\Model\Exceptions\DuplicatedCountryException;

// CountryScenarios - класс с методами работы с объектами стран
class CountryScenarios
{
    public function __construct(
        private readonly CountryRepository $storage
    ) {
    }

    // getAll - получение всех стран
    // вход: -
    // выход: список объектов Country
    public function getAll(): array
    {
        return $this->storage->selectAll();
    }

    // get - получение страны по коду
    // вход: код страны (двухбуквенный, трехбуквенный или числовой)
    // выход: объект извлеченной страны
    // исключения: InvalidCodeException (400), CountryNotFoundException (404)
    public function get(string $code): Country
    {
        // Определить тип кода и выполнить проверку корректности
        $type = $this->determineCodeType($code);
        if ($type === null) {
            throw new InvalidCodeException("Недопустимый формат кода: {$code}");
        }
        // Получить страну по данному коду
        $country = match ($type) {
            'alpha2' => $this->storage->selectByAlpha2($code),
            'alpha3' => $this->storage->selectByAlpha3($code),
            'numeric' => $this->storage->selectByNumeric($code),
        };
        if ($country === null) {
            // Если страна не найдена - выбросить ошибку 404
            throw new CountryNotFoundException($code);
        }
        // Вернуть полученную страну
        return $country;
    }

    // store - сохранение новой страны
    // вход: объект страны
    // выход: -
    // исключения: InvalidCodeException (400), DuplicatedCodeException (409), DuplicatedCountryException (409), InvalidCountryException (400)
    public function store(Country $country): void
    {
        
        $validationErrors = [];

        // Проверки уникальности кодов 
        if ($this->storage->selectByAlpha2($country->getIsoAlpha2()) !== null) {
            throw new DuplicatedCodeException($country->getIsoAlpha2());
        }
        if ($this->storage->selectByAlpha3($country->getIsoAlpha3()) !== null) {
            throw new DuplicatedCodeException($country->getIsoAlpha3());
        }
        if ($this->storage->selectByNumeric((string)$country->getIsoNumeric()) !== null) {
            throw new DuplicatedCodeException((string)$country->getIsoNumeric());
        }

        // Проверки уникальности названий
        if ($this->storage->selectByShortName($country->getShortName()) !== null) {
            throw new DuplicatedCountryException('Короткое название должно быть уникальным: ' . $country->getShortName());
        }
        if ($this->storage->selectByFullName($country->getFullName()) !== null) {
            throw new DuplicatedCountryException('Полное название должно быть уникальным: ' . $country->getFullName());
        }

      
        if (!empty($validationErrors)) {
            throw new InvalidCountryException('Недопустимые данные для сохранения страны', $validationErrors);
        }

  
        $this->storage->save($country);
    }

    // edit - редактирование страны по коду
    // вход: код редактируемой страны (URL-параметр, может быть двухбуквенным, трехбуквенным или числовым), объект обновленной страны
    // выход: -
    // исключения:
    // - InvalidCodeException (400): невалидный код или невалидные данные для редактирования
    // - CountryNotFoundException (404): не найдена страна по валидному коду
    // - InvalidCountryException (400): коды страны пытаются изменить (не разрешено)
    // - DuplicatedCountryException (409): дублирование названий с существующими данными
    public function edit(string $code, Country $country): void
    {
        // Определить тип кода и выполнить проверку корректности
        $type = $this->determineCodeType($code);
        if ($type === null) {
            throw new InvalidCodeException("Недопустимый формат кода: {$code}");
        }
        // Выполнить проверку наличия страны для редактирования
        $existingCountry = match ($type) {
            'alpha2' => $this->storage->selectByAlpha2($code),
            'alpha3' => $this->storage->selectByAlpha3($code),
            'numeric' => $this->storage->selectByNumeric($code),
        };
        if ($existingCountry === null) {
            // Если страна не найдена - выбросить ошибку 404
            throw new CountryNotFoundException($code);
        }

  
        $validationErrors = [];

        // Перед проверками кодов
        $existingIsoNumeric = str_pad((string)$existingCountry->getIsoNumeric(), 3, '0', STR_PAD_LEFT);
        $countryIsoNumeric = str_pad((string)$country->getIsoNumeric(), 3, '0', STR_PAD_LEFT);
        if ($countryIsoNumeric !== $existingIsoNumeric) {
            $validationErrors[] = ['field' => 'isoNumeric', 'message' => 'Коды страны нельзя изменять'];
        }
       
        $existingIsoAlpha2 = strtoupper($existingCountry->getIsoAlpha2());
        $countryIsoAlpha2 = strtoupper($country->getIsoAlpha2());
        if ($countryIsoAlpha2 !== $existingIsoAlpha2) {
            $validationErrors[] = ['field' => 'isoAlpha2', 'message' => 'Коды страны нельзя изменять'];
        }

        $existingIsoAlpha3 = strtoupper($existingCountry->getIsoAlpha3());
        $countryIsoAlpha3 = strtoupper($country->getIsoAlpha3());
        if ($countryIsoAlpha3 !== $existingIsoAlpha3) {
            $validationErrors[] = ['field' => 'isoAlpha3', 'message' => 'Коды страны нельзя изменять'];
        }

        // Выполнить проверку уникальности названий
        if ($country->getShortName() !== $existingCountry->getShortName() &&
            $this->storage->selectByShortName($country->getShortName()) !== null) {
            throw new DuplicatedCountryException('Короткое название должно быть уникальным: ' . $country->getShortName());
        }
        if ($country->getFullName() !== $existingCountry->getFullName() &&
            $this->storage->selectByFullName($country->getFullName()) !== null) {
            throw new DuplicatedCountryException('Полное название должно быть уникальным: ' . $country->getFullName());
        }

  
        if (!empty($validationErrors)) {
            throw new InvalidCountryException('Недопустимые данные для редактирования страны', $validationErrors, 4);
        }

  
        $this->storage->update($code, $country);
    }

    // delete - удаление страны по коду
    // вход: код удаляемой страны (URL-параметр, может быть двухбуквенным, трехбуквенным или числовым)
    // выход: -
    // исключения:
    // - InvalidCodeException (400): невалидный код
    // - CountryNotFoundException (404): не найдена страна по валидному коду
    public function delete(string $code): void
    {
        // Определить тип кода и выполнить проверку корректности
        $type = $this->determineCodeType($code);
        if ($type === null) {
            throw new InvalidCodeException("Недопустимый формат кода: {$code}");
        }
        // Выполнить проверку наличия страны для удаления
        $country = match ($type) {
            'alpha2' => $this->storage->selectByAlpha2($code),
            'alpha3' => $this->storage->selectByAlpha3($code),
            'numeric' => $this->storage->selectByNumeric($code),
        };
        if ($country === null) {
            // Если страна не найдена - выбросить ошибку 404
            throw new CountryNotFoundException($code);
        }
  
        $this->storage->delete($code);
    }

    // determineCodeType - определение типа кода
    // вход: строка кода
    // выход: 'alpha2', 'alpha3', 'numeric' или null
    private function determineCodeType(string $code): ?string
    {
        if ($this->validateAlpha2($code)) {
            return 'alpha2';
        }
        if ($this->validateAlpha3($code)) {
            return 'alpha3';
        }
        if ($this->validateNumeric($code)) {
            return 'numeric';
        }
        return null;
    }

    // validateAlpha2 - проверка корректности двухбуквенного кода
    // вход: строка кода
    // выход: true если строка корректная, иначе false
    private function validateAlpha2(string $code): bool
    {
        return preg_match('/^[A-Z]{2}$/', $code);
    }

    // validateAlpha3 - проверка корректности трехбуквенного кода
    // вход: строка кода
    // выход: true если строка корректная, иначе false
    private function validateAlpha3(string $code): bool
    {
        return preg_match('/^[A-Z]{3}$/', $code);
    }

    // validateNumeric - проверка корректности числового кода
    // вход: строка кода
    // выход: true если строка корректная, иначе false
    private function validateNumeric(string $code): bool
    {
        return preg_match('/^[0-9]{3}$/', $code);
    }
}
