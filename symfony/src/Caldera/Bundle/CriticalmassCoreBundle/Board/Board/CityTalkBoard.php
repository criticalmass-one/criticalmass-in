<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Board\Board;

use Caldera\Bundle\CriticalmassModelBundle\Entity\City;

class CityTalkBoard implements BoardInterface
{
    /**
     * @var City $city
     */
    protected $city;

    public function __construct()
    {

    }

    public function setCity(City $city)
    {
        $this->city = $city;
    }

    public function getTitle()
    {
        return 'Allgemeines über die '.$this->city->getTitle();
    }

    public function getDescription()
    {
        return null;
    }

    public function getThreadNumber()
    {
        return 0;
    }

    public function getPostNumber()
    {
        return 0;
    }

    public function getLastPost()
    {
        return null;
    }
}