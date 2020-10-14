<?php declare(strict_types=1);

namespace App\Criticalmass\RideNamer;

use App\Entity\Ride;

class GermanCityDateRideNamer implements RideNamerInterface
{
    public function generateTitle(Ride $ride): string
    {
        $cityTitle = $ride->getCity()->getTitle();
        $date = $ride->getDateTime()->format('d.m.Y');

        return sprintf('%s %s', $cityTitle, $date);
    }
}
