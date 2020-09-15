<?php declare(strict_types=1);

namespace App\Criticalmass\Wikidata\CityTimezoneDetector;

use App\Entity\City;

interface CityTimezoneDetectorInterface
{
    public function queryForCity(City $city): ?string;
}
