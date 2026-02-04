<?php declare(strict_types=1);

namespace App\Criticalmass\Rating\StarGenerator;

use App\Entity\Ride;

interface StarGeneratorInterface
{
    public function generateForRide(Ride $ride): string;
}
