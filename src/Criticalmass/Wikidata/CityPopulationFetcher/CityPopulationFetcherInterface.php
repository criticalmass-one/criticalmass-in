<?php declare(strict_types=1);

namespace App\Criticalmass\Wikidata\CityPopulationFetcher;

use App\Entity\City;

interface CityPopulationFetcherInterface
{
    public function fetch(City $city): ?int;
}