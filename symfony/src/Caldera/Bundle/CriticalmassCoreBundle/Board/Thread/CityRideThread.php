<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Board\Thread;

use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Post;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;

class CityRideThread extends BaseThread
{
    /**
     * @var Ride $ride
     */
    protected $ride;

    public function setRide(Ride $ride)
    {
        $this->ride = $ride;

        return $this;
    }

    public function getRide()
    {
        return $this->ride;
    }

    public function getTitle()
    {
        return 'Kommentare zur Tour am ' . $this->ride->getDateTime()->format('d.m.Y');
    }

    public function getViewNumber()
    {
        return 0;
    }
}