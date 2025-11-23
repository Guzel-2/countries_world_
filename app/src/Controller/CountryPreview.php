<?php

namespace App\Controller;

class CountryPreview
{
    public function __construct(
        public string $shortName,
        public string $uri
    ) {}
}
