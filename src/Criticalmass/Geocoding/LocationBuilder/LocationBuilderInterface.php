<?php declare(strict_types=1);

namespace App\Criticalmass\Geocoding\LocationBuilder;

use App\Criticalmass\Geocoding\ReverseGeocodeable;
use Geocoder\Location;

interface LocationBuilderInterface
{
    public function build(ReverseGeocodeable $reverseGeocodeable, Location $location): ReverseGeocodeable;
}
