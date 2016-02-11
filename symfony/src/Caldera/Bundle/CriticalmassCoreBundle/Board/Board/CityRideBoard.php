<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Board\Board;

use Caldera\Bundle\CriticalmassModelBundle\Entity\City;

class CityRideBoard implements BoardInterface
{
    /**
     * @var City $city
     */
    protected $city;

    protected $rides;

    protected $posts;

    public function __construct()
    {

    }

    public function setCity(City $city)
    {
        $this->city = $city;

        return $this;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setRides($rides)
    {
        $this->rides = $rides;
    }

    public function setPosts($posts)
    {
        $this->posts = $posts;
    }

    public function getTitle()
    {
        return 'Touren der '.$this->city->getTitle();
    }

    public function getDescription()
    {
        return null;
    }

    public function getThreadNumber()
    {
        return count($this->rides);
    }

    public function getPostNumber()
    {
        return count($this->posts);
    }

    public function getLastPost()
    {
        return null;
    }
}