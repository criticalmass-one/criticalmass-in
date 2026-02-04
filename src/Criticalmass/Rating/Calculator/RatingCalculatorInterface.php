<?php declare(strict_types=1);

namespace App\Criticalmass\Rating\Calculator;

use App\Entity\Ride;

interface RatingCalculatorInterface
{
    public function calculateRide(Ride $ride): ?float;
}