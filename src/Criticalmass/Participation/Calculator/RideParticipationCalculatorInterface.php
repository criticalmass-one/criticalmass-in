<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Participation\Calculator;

use AppBundle\Entity\Ride;

interface RideParticipationCalculatorInterface
{
    public function setRide(Ride $ride): RideParticipationCalculator;
    public function calculate(): RideParticipationCalculator;
}
