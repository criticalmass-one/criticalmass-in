<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Board\Thread;

use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;

class CityRideThread
{
    /**
     * @var City $city
     */
    protected $city;

    /**
     * @var Ride $ride
     */
    protected $ride;

    protected $posts;

    public function __construct()
    {

    }

    public function setRide(Ride $ride)
    {
        $this->ride = $ride;

        return $this;
    }

    public function setPosts($posts)
    {
        $this->posts = $posts;
    }

    public function getTitle()
    {
        return 'Kommentare zur Tour am '.$this->ride->getDateTime()->format('d.m.Y');
    }

    public function getDescription()
    {
        return null;
    }

    public function getLastPost()
    {
        return null;
    }
}