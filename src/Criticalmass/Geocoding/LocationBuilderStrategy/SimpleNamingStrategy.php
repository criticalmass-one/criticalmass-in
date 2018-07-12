<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Geocoding\LocationBuilderStrategy;

use Geocoder\Location;

class SimpleNamingStrategy implements LocationBuilderStrategyInterface
{
    public function buildLocation(Location $location): string
    {
        return sprintf('%s, %s', $location->getStreetName(), $location->getAdminLevels()->first()->getName());
    }
}
