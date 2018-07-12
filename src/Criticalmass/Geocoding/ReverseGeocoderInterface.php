<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Geocoding;

interface ReverseGeocoderInterface
{
    public function reverseGeocode(ReverseGeocodeable $geocodeable): ReverseGeocodeable;
}
