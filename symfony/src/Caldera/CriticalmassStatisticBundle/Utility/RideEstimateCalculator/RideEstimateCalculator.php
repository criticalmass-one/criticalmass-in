<?php
/**
 * Created by PhpStorm.
 * User: maltehuebner
 * Date: 16.09.14
 * Time: 18:55
 */

namespace Caldera\CriticalmassStatisticBundle\Utility\RideEstimateCalculator;


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

    }

    public function getRide()
    {
        return $this->ride;
    }
} 