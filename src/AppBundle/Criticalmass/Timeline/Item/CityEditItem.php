<?php

namespace AppBundle\Criticalmass\Timeline\Item;

use AppBundle\Entity\City;
use AppBundle\Entity\User;

class CityEditItem extends AbstractItem
{
    /** @var User $user */
    protected $user;

    /** @var City $city */
    protected $city;

    /** @var string $cityName */
    protected $cityName;

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): CityEditItem
    {
        $this->user = $user;

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
