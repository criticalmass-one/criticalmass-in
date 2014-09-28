<?php
/**
 * Created by PhpStorm.
 * User: maltehuebner
 * Date: 16.09.14
 * Time: 18:55
 */

namespace Caldera\CriticalmassStatisticBundle\Utility\RideEstimateCalculator;


use Caldera\CriticalmassCoreBundle\Entity\Ride;

class RideEstimateCalculator {
    protected $ride;
    protected $estimates = array();

    public function __construct()
    {

    }

    public function setRide(Ride $ride)
    {
        $this->ride = $ride;
    }

    public function setEstimates($estimates)
    {
        $this->estimates = $estimates;
    }

    public function calculate()
    {
        $this->calculateDistance();
        $this->calculateDuration();
        $this->calculateParticipants();
    }

    protected function calculateDistance()
    {
        $distance = 0;
        $distanceCounter = 0;

        foreach ($this->estimates as $estimate)
        {
            if ($estimate->getEstimatedDistance())
            {
                $distance += $estimate->getEstimatedDistance();
                ++$distanceCounter;
            }
        }

        if ($distanceCounter)
        {
            $distance /= $distanceCounter;
        }

        $this->ride->setEstimatedDistance($distance);
    }

    protected function calculateDuration()
    {
        $duration = 0;
        $durationCounter = 0;

        foreach ($this->estimates as $estimate)
        {
            if ($estimate->getEstimatedDuration())
            {
                $duration += $estimate->getEstimatedDuration();
                ++$durationCounter;
            }
        }

        if ($durationCounter)
        {
            $duration /= $durationCounter;
        }

        $this->ride->setEstimatedDuration($duration);
    }

    protected function calculateParticipants()
    {
        $participants = 0;
        $participantsCounter = 0;

        foreach ($this->estimates as $estimate)
        {
            if ($estimate->getEstimatedParticipants())
            {
                $participants += $estimate->getEstimatedParticipants();
                ++$participantsCounter;
            }
        }

        if ($participantsCounter)
        {
            $participants /= $participantsCounter;
        }

        $this->ride->setEstimatedParticipants($participants);
    }

    public function getRide()
    {
        return $this->ride;
    }
} 