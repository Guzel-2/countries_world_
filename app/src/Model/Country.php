<?php

namespace App\Model;

class Country {
    private string $shortName;
    private string $fullName;
    private string $isoAlpha2;
    private string $isoAlpha3;
    private string $isoNumeric;
    private int $population;
    private int $square;

    public function __construct(array $data) {
      
        $this->shortName = (string) ($data['shortName'] ?? '');
        $this->fullName = (string) ($data['fullName'] ?? '');
        $this->isoAlpha2 = (string) ($data['isoAlpha2'] ?? '');
        $this->isoAlpha3 = (string) ($data['isoAlpha3'] ?? '');
        $this->isoNumeric = (string) ($data['isoNumeric'] ?? '');  
        
        // Приведение для числовых полей
        $this->population = (int) ($data['population'] ?? 0);  
        $this->square = (int) ($data['square'] ?? 0);          
    }

    // Геттеры
    public function getShortName(): string {
        return $this->shortName;
    }

    public function getFullName(): string {
        return $this->fullName;
    }

    public function getIsoAlpha2(): string {
        return $this->isoAlpha2;
    }

    public function getIsoAlpha3(): string {
        return $this->isoAlpha3;
    }

    public function getIsoNumeric(): string {
        return $this->isoNumeric;
    }

    public function getPopulation(): int {
        return $this->population;
    }

    public function getSquare(): int {
        return $this->square;
    }
}
