<?php declare(strict_types=1);

namespace App\Criticalmass\Participation\Calculator;

use App\Entity\Participation;
use App\Entity\Ride;
use App\Repository\ParticipationRepository;

class RideParticipationCalculator extends AbstractRideParticipationCalculator
{
    public function setRide(Ride $ride): RideParticipationCalculator
    {
        $this->ride = $ride;

        return $this;
    }

    public function calculate(): RideParticipationCalculator
    {
        $this->ride
            ->setParticipationsNumberYes($this->countParticipationsForRide('yes'))
            ->setParticipationsNumberMaybe($this->countParticipationsForRide('maybe'))
            ->setParticipationsNumberNo($this->countParticipationsForRide('no'));

        $this->registry->getManager()->flush();

        return $this;
    }

    protected function countParticipationsForRide(string $type): int
    {
        return $this->getParticipationRepository()->countParticipationsForRide($this->ride, $type);
    }

    protected function getParticipationRepository(): ParticipationRepository
    {
        return $this->registry->getRepository(Participation::class);
    }
}
