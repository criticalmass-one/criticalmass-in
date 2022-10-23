<?php declare(strict_types=1);

namespace App\Criticalmass\OpenStreetMap\NominatimCityBridge;

use App\Entity\City;

interface NominatimCityBridgeInterface
{
    public function lookupCity(string $citySlug): ?City;
}
