<?php declare(strict_types=1);

namespace App\Criticalmass\Geocoding\LocationBuilderStrategy;

use Geocoder\Location;

interface LocationBuilderStrategyInterface
{
    public function buildLocation(Location $location): ?string;
}
