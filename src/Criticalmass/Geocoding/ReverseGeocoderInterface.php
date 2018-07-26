<?php declare(strict_types=1);

namespace App\Criticalmass\Geocoding;

interface ReverseGeocoderInterface
{
    public function reverseGeocode(ReverseGeocodeable $geocodeable): ReverseGeocodeable;
}
