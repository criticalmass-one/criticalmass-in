<?php

namespace AppBundle\Criticalmass\Timeline\Item;

use AppBundle\Entity\City;

class CityCreatedItem extends AbstractItem
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

    public function setUsername(string $username): CityCreatedItem
    {
        $this->username = $username;

        return $this;
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function setCity(City $city): CityCreatedItem
    {
        $this->city = $city;

        return $this;
    }

    public function getCityName(): string
    {
        return $this->cityName;
    }

    public function setCityName(string $cityName): CityCreatedItem
    {
        $this->cityName = $cityName;

        return $this;
    }
}
