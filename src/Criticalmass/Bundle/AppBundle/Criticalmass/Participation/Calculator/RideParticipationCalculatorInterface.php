<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\Criticalmass\Participation\Calculator;

use Criticalmass\Bundle\AppBundle\Entity\Ride;

interface RideParticipationCalculatorInterface
{
    public function setRide(Ride $ride): RideParticipationCalculator;
    public function calculate(): RideParticipationCalculator;
}
