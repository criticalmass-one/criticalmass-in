<?php

namespace Criticalmass\Bundle\AppBundle\Criticalmass\OpenStreetMap\NominatimCityBridge;

use Criticalmass\Bundle\AppBundle\Entity\City;

interface NominatimCityBridgeInterface
{
    public function lookupCity(string $citySlug): ?City;
}
