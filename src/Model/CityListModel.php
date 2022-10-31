<?php declare(strict_types=1);

namespace App\Model;

use App\Entity\City;
use App\Entity\Ride;

class CityListModel
{
    public function __construct(protected City $city, protected Ride $currentRide = null, protected array $cycles = [], protected int $countRides = 0)
    {
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function getCurrentRide(): ?Ride
    {
        return $this->currentRide;
    }

    public function getCycles(): array
    {
        return $this->cycles;
    }

    public function countRides(): int
    {
        return $this->countRides;
    }
}
