<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Item;

use App\Entity\City;

class CityCreatedItem extends AbstractItem
{
    protected ?City $city = null;

    protected ?string $cityName = null;

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
