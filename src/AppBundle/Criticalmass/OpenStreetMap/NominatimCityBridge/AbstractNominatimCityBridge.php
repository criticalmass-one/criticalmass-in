<?php

namespace AppBundle\Criticalmass\OpenStreetMap\NominatimCityBridge;

use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;

abstract class AbstractNominatimCityBridge
{
    const NOMINATIM_URL = 'https://nominatim.openstreetmap.org/';

    /** @var Doctrine $doctrine */
    protected $doctrine;

    public function __construct(Doctrine $doctrine)
    {
        $this->doctrine = $doctrine;
    }
}
