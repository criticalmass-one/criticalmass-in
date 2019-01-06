<?php declare(strict_types=1);

namespace App\Criticalmass\Geocoding\LocationBuilderStrategy;

use Geocoder\Location;

class SimpleNamingStrategy implements LocationBuilderStrategyInterface
{
    public function buildLocation(Location $location): ?string
    {
        if ($location->getStreetName() && $location->getAdminLevels()->count() !== 0 && $location->getAdminLevels()->first()) {
            return sprintf('%s, %s', $location->getStreetName(), $location->getAdminLevels()->first()->getName());
        }

        return null;
    }
}
