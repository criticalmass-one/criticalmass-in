<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Statistic\RideEstimate;

use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEstimate;

class RideEstimateCalculator
{
    /** @var Ride $ride */
    protected $ride;

    /** @var array $estimates */
    protected $estimates = [];

    public function setEstimates(array $estimates): RideEstimateCalculator
    {
        $this->estimates = $estimates;

        return $this;
    }

    public function calculate(): RideEstimateCalculator
    {
        $this->calculateDistance();
        $this->calculateDuration();
        $this->calculateParticipants();

        return $this;
    }

    protected function calculateDistance(): RideEstimateCalculator
    {
        $distance = 0.0;
        $distanceCounter = 0;

        /** @var RideEstimate $estimate */
        foreach ($this->estimates as $estimate) {
            if ($estimate->getEstimatedDistance()) {
                $distance += $estimate->getEstimatedDistance();

                ++$distanceCounter;
            }
        }

        if ($distanceCounter) {
            $distance /= $distanceCounter;
        }

        $this->ride->setEstimatedDistance($distance);

        return $this;
    }

    protected function calculateDuration(): RideEstimateCalculator
    {
        $duration = 0.0;
        $durationCounter = 0;

        foreach ($this->estimates as $estimate) {
            if ($estimate->getEstimatedDuration()) {
                $duration += $estimate->getEstimatedDuration();

                ++$durationCounter;
            }
        }

        if ($durationCounter) {
            $duration /= $durationCounter;
        }

        $this->ride->setEstimatedDuration($duration);

        return $this;
    }

    protected function calculateParticipants(): RideEstimateCalculator
    {
        $participants = 0;
        $participantsCounter = 0;

        foreach ($this->estimates as $estimate) {
            if ($estimate->getEstimatedParticipants()) {
                $participants += $estimate->getEstimatedParticipants();

                ++$participantsCounter;
            }
        }

        if ($participantsCounter) {
            $participants = (int)ceil($participants / $participantsCounter);
        }

        $this->ride->setEstimatedParticipants($participants);

        return $this;
    }

    public function getRide(): Ride
    {
        return $this->ride;
    }

    public function setRide(Ride $ride): RideEstimateCalculator
    {
        $this->ride = $ride;

        return $this;
    }
} 
