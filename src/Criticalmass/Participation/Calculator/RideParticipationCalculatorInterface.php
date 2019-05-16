<?php declare(strict_types=1);

namespace App\Criticalmass\Participation\Calculator;

use App\Entity\Ride;

interface RideParticipationCalculatorInterface
{
    public function setRide(Ride $ride): RideParticipationCalculator;
    public function calculate(): RideParticipationCalculator;
}
