<?php declare(strict_types=1);

namespace App\Criticalmass\Statistic\RideEstimateConverter;

use App\Entity\RideEstimate;
use App\Entity\Track;
use Symfony\Bridge\Doctrine\RegistryInterface;

class RideEstimateConverter implements RideEstimateConverterInterface
{
    /** @var RegistryInterface $registry */
    protected $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function addEstimateFromTrack(Track $track, bool $flush = true): RideEstimateConverterInterface
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

            $this->registry->getManager()->persist($re);

            if ($flush) {
                $this->registry->getManager()->flush();
            }
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
}