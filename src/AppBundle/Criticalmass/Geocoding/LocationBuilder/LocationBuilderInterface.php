<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Geocoding\LocationBuilder;

use AppBundle\Criticalmass\Geocoding\ReverseGeocodeable;
use Geocoder\Location;

interface LocationBuilderInterface
{
    public function build(ReverseGeocodeable $reverseGeocodeable, Location $location): ReverseGeocodeable;
}
