<?php

namespace Criticalmass\Component\RideGenerator\RideGenerator;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\CityCycle;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Component\Util\DateTimeUtil;

class RideGenerator extends AbstractRideGenerator
{
    public function execute(): RideGeneratorInterface
    {
        if (!count($this->cityList)) {
            $this->cityList = $this->findCities();
        }

        foreach ($this->cityList as $city) {
            $cycles = $this->findCyclesForCity($city);

            $createdRides = $this->processCityCycles($cycles);

            $this->rideList = $this->rideList + $createdRides;
        }

        return $this;
    }

    protected function findCities(): array
    {
        return $this->doctrine->getRepository(City::class)->findCities();
    }

    protected function findCyclesForCity(City $city): array
    {
        $dateTimeSpec = sprintf('%d-%d-01 00:00:00', $this->year, $this->month);
        $startDateTime = new \DateTime($dateTimeSpec);
        $endDateTime = DateTimeUtil::getMonthEndDateTime($startDateTime);

        return $this->doctrine->getRepository(CityCycle::class)->findByCity(
            $city,
            $startDateTime,
            $endDateTime
        );
    }

    protected function processCityCycles(array $cycles): array
    {
        return $this->rideCalculator
            ->reset()
            ->setCycleList($cycles)
            ->setYear($this->year)
            ->setMonth($this->month)
            ->execute()
            ->getRideList();
    }

    protected function hasRideAlreadyBeenCreated(CityCycle $cityCycle): bool
    {
        $dateTimeSpec = sprintf('%d-%d-01 00:00:00');
        $startDateTime = new \DateTime($dateTimeSpec);
        $endDateTime = DateTimeUtil::getMonthEndDateTime($startDateTime);

        $existingRides = $this->doctrine->getRepository(Ride::class)->findRidesByCycleInInterval($cityCycle, $this->startDateTime, $this->endDateTime);


        return count($existingRides) > 0;
    }
}
