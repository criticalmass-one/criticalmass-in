<?php declare(strict_types=1);

namespace App\Criticalmass\RideNamer;

use App\Entity\Ride;

class IsoCityDateRideNamer implements RideNamerInterface
{
    public function generateTitle(Ride $ride): string
    {
        $cityTitle = $ride->getCity()->getTitle();
        $date = $ride->getDateTime()->format('Y-m-d');

        return sprintf('%s %s', $cityTitle, $date);
    }
}
