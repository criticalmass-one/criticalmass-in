<?php declare(strict_types=1);

namespace Criticalmass\Component\Statistic\RideEstimate;

use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Bundle\AppBundle\Entity\RideEstimate;
use Criticalmass\Bundle\AppBundle\Entity\Track;
use Doctrine\ORM\EntityManager;

class RideEstimateService
{
    /** @var EntityManager $entityManager */
    protected $entityManager;

    /** @var RideEstimateCalculator $calculator */
    protected $calculator;

    public function __construct(EntityManager $entityManager, RideEstimateCalculator $calculator)
    {
        $this->entityManager = $entityManager;
        $this->calculator = $calculator;
    }

    public function flushEstimates(Ride $ride): RideEstimateService
    {
        $ride
            ->setEstimatedDistance(0.0)
            ->setEstimatedDuration(0.0)
            ->setEstimatedParticipants(0)
        ;

        $this->entityManager->flush();

        return $this;
    }

    public function calculateEstimates(Ride $ride): RideEstimateService
    {
        $estimates = $this->entityManager->getRepository(RideEstimate::class)->findByRide($ride);

        $this->calculator
            ->setRide($ride)
            ->setEstimates($estimates)
            ->calculate()
        ;

        $this->entityManager->flush();

        return $this;
    }

    public function addEstimateFromTrack(Track $track): RideEstimateService
    {
        $re = new RideEstimate();
        $re
            ->setRide($track->getRide())
            ->setUser($track->getUser())
            ->setTrack($track)
            ->setEstimatedDistance($track->getDistance())
            ->setEstimatedDuration($this->calculateDurationInHours($track))
        ;

        $track->setRideEstimate($re);

        $this->entityManager->persist($re);
        $this->entityManager->flush();

        return $this;
    }

    protected function calculateDurationInSeconds(Track $track): int
    {
        if ($track->getStartDateTime() && $track->getEndDateTime()) {
            return $track->getStartDateTime()->getTimestamp() - $track->getEndDateTime()->getTimestamp();
        }

        return 0;
    }

    protected function calculateDurationInHours(Track $track): float
    {
        if ($durationInSeconds = $this->calculateDurationInSeconds($track)) {
            return $durationInSeconds / 3600.0;
        }

        return 0;
    }
}
