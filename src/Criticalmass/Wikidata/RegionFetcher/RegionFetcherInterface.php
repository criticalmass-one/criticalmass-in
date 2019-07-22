<?php declare(strict_types=1);

namespace App\Criticalmass\Wikidata\RegionFetcher;

use App\Entity\Region;

interface RegionFetcherInterface
{
    public function fetch(Region $region): ?array;
}