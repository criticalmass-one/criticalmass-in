<?php declare(strict_types=1);

namespace App\Factory;

use App\Entity\City;
use App\Entity\CityCycle;
use App\Entity\Ride;
use App\Model\CityListModel;
use Doctrine\Persistence\ManagerRegistry;

class CityListFactory
{
    protected $list = [];

    public function __construct(protected ManagerRegistry $doctrine)
    {
    }

    public function getList(): array
    {
        if (!count($this->list)) {
            $this->createList();
        }

        return $this->list;
    }

    protected function createList(): CityListFactory
    {
        $cities = $this->doctrine->getRepository(City::class)->findEnabledCities();
        $now = new \DateTime();

        foreach ($cities as $city) {
            $currentRide = $this->doctrine->getRepository(Ride::class)->findCurrentRideForCity($city);
            $countRides = $this->doctrine->getRepository(Ride::class)->countRidesByCity($city);
            $cycles = $this->doctrine->getRepository(CityCycle::class)->findByCity($city, $now, $now);

            $this->list[] = $this->createModel($city, $currentRide, $cycles, $countRides);
        }

        return $this;
    }

    protected function createModel(
        City $city,
        Ride $currentRide = null,
        array $cycles = [],
        int $countRides = 0
    ): CityListModel {
        return new CityListModel($city, $currentRide, $cycles, $countRides);
    }
}
