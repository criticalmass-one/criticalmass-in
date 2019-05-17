<?php declare(strict_types=1);

namespace App\Criticalmass\Geocoding;

use Geocoder\Location;

interface ReverseGeocoderInterface
{
    public function query(ReverseGeocodeable $geocodeable): ?Location;
    public function reverseGeocode(ReverseGeocodeable $geocodeable): ReverseGeocodeable;
}
