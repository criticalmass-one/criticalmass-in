<?php

namespace AppBundle\Factory;

use AppBundle\Entity\City;
use AppBundle\Entity\Ride;
use AppBundle\Model\CityListModel;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;

class CityListFactory
{
    protected $doctrine;

    protected $list = [];

    public function __construct(Doctrine $doctrine)
    {
        $this->doctrine = $doctrine;
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

        foreach ($cities as $city) {
            $currentRide = $this->doctrine->getRepository(Ride::class)->findCurrentRideForCity($city);
            $countRides = $this->doctrine->getRepository(Ride::class)->countRidesByCity($city);

            $this->list[] = $this->createModel($city, $currentRide, $countRides);
        }

        return $this;
    }

    protected function createModel(City $city, Ride $currentRide = null, int $countRides = 0): CityListModel
    {
        return new CityListModel($city, $currentRide, $countRides);
    }
}
