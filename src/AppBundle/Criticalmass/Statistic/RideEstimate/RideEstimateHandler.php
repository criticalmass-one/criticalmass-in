<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Statistic\RideEstimate;

use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEstimate;
use AppBundle\Entity\Track;
use AppBundle\Repository\RideEstimateRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;

class RideEstimateHandler
{
    /** @var Registry $registry */
    protected $registry;

    /** @var RideEstimateCalculator $calculator */
    protected $calculator;

    /** @var Ride $ride */
    protected $ride;

    public function __construct(Registry $registry, RideEstimateCalculator $calculator)
    {
        $this->registry = $registry;
        $this->calculator = $calculator;
    }

    public function setRide(Ride $ride): RideEstimateHandler
    {
        $this->ride = $ride;

        return $this;
    }

    public function flushEstimates(): RideEstimateHandler
    {
        $this->ride
            ->setEstimatedDistance(0.0)
            ->setEstimatedDuration(0.0)
            ->setEstimatedParticipants(0);

        $this->getEntityManager()->flush();

        return $this;
    }

    public function calculateEstimates(): RideEstimateHandler
    {
        $estimates = $this->getRideEstimateRepository()->findByRide($this->ride);

        $this->calculator
            ->setRide($this->ride)
            ->setEstimates($estimates)
            ->calculate();

        $this->getEntityManager()->flush();

        return $this;
    }

    public function addEstimateFromTrack(Track $track): RideEstimateHandler
    {
        if ($track->getRideEstimate()) {
            $re = $track->getRideEstimate();
        } else {
            $re = new RideEstimate();
            $re
                ->setRide($track->getRide())
                ->setUser($track->getUser())
                ->setTrack($track)
                ->setEstimatedDistance($track->getDistance())
                ->setEstimatedDuration($this->calculateDurationInHours($track));

            $track->setRideEstimate($re);

            $this->getEntityManager()->persist($re);
            $this->getEntityManager()->flush();
        }

        return $this;
    }

    protected function calculateDurationInSeconds(Track $track): int
    {
        if ($track->getStartDateTime() && $track->getEndDateTime()) {
            return $track->getEndDateTime()->getTimestamp() - $track->getStartDate()->getTimestamp();
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

    protected function getRideEstimateRepository(): RideEstimateRepository
    {
        return $this->registry->getRepository(RideEstimate::class);
    }

    protected function getEntityManager(): ObjectManager
    {
        return $this->registry->getManager();
    }
}
