<?php

namespace Caldera\CriticalmassCoreBundle\Utility\StandardRideGenerator;

use Caldera\CriticalmassCoreBundle\Entity\Ride;

class StandardRideGenerator {
    protected $year;
    protected $month;
    protected $city;
    protected $ride;

    public function __construct($city, $year, $month)
    {
        $this->year = $year;
        $this->month = $month;
        $this->city = $city;
        $this->ride = new Ride();
    }
} 