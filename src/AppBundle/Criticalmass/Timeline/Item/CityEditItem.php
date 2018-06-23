<?php

namespace AppBundle\Criticalmass\Timeline\Item;

use AppBundle\Entity\City;

class CityEditItem extends AbstractItem
{
    /** @var string $username */
    protected $username;

    /** @var City $city */
    protected $city;

    /** @var string $cityName */
    protected $cityName;

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): CityEditItem
    {
        $this->username = $username;

        return $this;
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function setCity(City $city): CityEditItem
    {
        $this->city = $city;

        return $this;
    }

    public function getCityName(): string
    {
        return $this->cityName;
    }

    public function setCityName(string $cityName): CityEditItem
    {
        $this->cityName = $cityName;

        return $this;
    }
}
