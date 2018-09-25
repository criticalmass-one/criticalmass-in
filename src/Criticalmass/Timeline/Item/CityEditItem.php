<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Item;

use App\Entity\City;

class CityEditItem extends AbstractItem
{
    /** @var City $city */
    protected $city;

    /** @var string $cityName */
    protected $cityName;

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
