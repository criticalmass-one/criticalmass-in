<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item;

use Caldera\Bundle\CalderaBundle\Entity\City;

class CityEditItem extends AbstractItem
{
    /**
     * @var string $username
     */
    protected $username;

    /**
     * @var City $city
     */
    protected $city;

    /**
     * @var string $cityName
     */
    protected $cityName;

    /**
     * @var string $archiveMessage
     */
    protected $archiveMessage;

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param City $city
     */
    public function setCity(City $city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string
     */
    public function getCityName()
    {
        return $this->cityName;
    }

    /**
     * @param string $cityTitle
     */
    public function setCityName($cityName)
    {
        $this->cityName = $cityName;

        return $this;
    }

    /**
     * @return string
     */
    public function getArchiveMessage()
    {
        return $this->archiveMessage;
    }

    /**
     * @param string $archiveMessage
     */
    public function setArchiveMessage($archiveMessage)
    {
        $this->archiveMessage = $archiveMessage;

        return $this;
    }
}