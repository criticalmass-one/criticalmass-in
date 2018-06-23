<?php

namespace AppBundle\Factory;

use AppBundle\Entity\City;
use AppBundle\Entity\CityCycle;
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
