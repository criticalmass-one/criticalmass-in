<?php

namespace AppBundle\Criticalmass\OpenStreetMap\NominatimCityBridge;

use AppBundle\Entity\City;

interface NominatimCityBridgeInterface
{
    public function lookupCity(string $citySlug): ?City;
}
