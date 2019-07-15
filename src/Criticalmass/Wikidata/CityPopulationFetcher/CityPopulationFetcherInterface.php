<?php declare(strict_types=1);

namespace App\Criticalmass\Wikidata\CityPopulationFetcher;

interface CityPopulationFetcherInterface
{
    public function fetch(string $cityName): ?int;
}