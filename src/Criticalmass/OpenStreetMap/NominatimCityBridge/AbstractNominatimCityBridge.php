<?php

namespace App\Criticalmass\OpenStreetMap\NominatimCityBridge;

use Symfony\Bridge\Doctrine\RegistryInterface;

abstract class AbstractNominatimCityBridge implements NominatimCityBridgeInterface
{
    const NOMINATIM_URL = 'https://nominatim.openstreetmap.org/';

    /** @var RegistryInterface $doctrine */
    protected $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }
}
