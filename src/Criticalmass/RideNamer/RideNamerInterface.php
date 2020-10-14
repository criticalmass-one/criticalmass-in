<?php declare(strict_types=1);

namespace App\Criticalmass\RideNamer;

use App\Entity\Ride;

interface RideNamerInterface
{
    public function generateTitle(Ride $ride): string;
}
