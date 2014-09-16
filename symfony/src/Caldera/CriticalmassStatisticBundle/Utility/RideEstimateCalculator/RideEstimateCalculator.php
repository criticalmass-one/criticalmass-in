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

        foreach ($this->estimates as $estimate)
        {
            $distance += $estimate->getEstimatedDistance();
        }

        $distance /= count($this->estimates);

        $this->ride->setEstimatedDistance($distance);
    }

    protected function calculateDuration()
    {
        $duration = 0;

        foreach ($this->estimates as $estimate)
        {
            $duration += $estimate->getEstimatedDuration();
        }

        $duration /= count($this->estimates);

        $this->ride->setEstimatedDuration($duration);
    }

    protected function calculateParticipants()
    {
        $participants = 0;

        foreach ($this->estimates as $estimate)
        {
            $participants += $estimate->getEstimatedParticipants();
        }

        $participants /= count($this->estimates);

        $this->ride->setEstimatedParticipants($participants);
    }

    public function getRide()
    {
        return $this->ride;
    }
} 