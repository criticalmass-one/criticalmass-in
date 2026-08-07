<?php declare(strict_types=1);

namespace App\Criticalmass\Ical;

use App\Entity\Ride;

interface RideIcalGeneratorInterface
{
    public function generateForRide(Ride $ride): RideIcalGeneratorInterface;

    /** @param Ride[] $rides */
    public function generateForRides(array $rides): RideIcalGeneratorInterface;

    public function getSerializedContent(): string;
}
