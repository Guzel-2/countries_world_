<?php

namespace App\Model;

use App\Model\Country;

// Интерфейс для хранилища стран
interface CountryRepository {
    public function selectAll(): array;
    public function selectByCode(string $code): ?Country;
    public function save(Country $country): void;
    public function delete(string $code): void;
    public function update(string $code, Country $country): void;
}
