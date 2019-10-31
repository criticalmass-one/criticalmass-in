<?php declare(strict_types=1);

namespace App\Criticalmass\Rating\Manager;

use App\Entity\Ride;

interface RatingManagerInterface
{
    public function rateRide(Ride $ride, int $stars): RatingManagerInterface;
}
