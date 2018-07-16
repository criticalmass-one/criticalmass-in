<?php declare(strict_types=1);

namespace App\Criticalmass\RideGenerator\RideGenerator;

use App\Entity\City;
use App\Entity\CityCycle;
use App\Entity\Ride;
use App\Criticalmass\Util\DateTimeUtil;

class RideGenerator extends AbstractRideGenerator
{
    public function execute(): RideGeneratorInterface
    {
        foreach ($this->cityList as $city) {
            $cycles = $this->findCyclesForCity($city);

            $createdRides = $this->processCityCycles($cycles);

            $this->rideList = array_merge($this->rideList, $createdRides);
        }

        return $this;
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
        $cycles = $this->removeCreatedCycles($cycles);

        return $this->rideCalculator
            ->reset()
            ->setCycleList($cycles)
            ->setYear($this->year)
            ->setMonth($this->month)
            ->execute()
            ->getRideList();
    }

    protected function removeCreatedCycles(array $cycles): array
    {
        foreach ($cycles as $key => $cycle) {
            if ($this->hasRideAlreadyBeenCreated($cycle)) {
                unset($cycles[$key]);
            }
        }

        return $cycles;
    }

    protected function hasRideAlreadyBeenCreated(CityCycle $cityCycle): bool
    {
        $dateTimeSpec = sprintf('%d-%d-01 00:00:00', $this->year, $this->month);
        $startDateTime = new \DateTime($dateTimeSpec);
        $endDateTime = DateTimeUtil::getMonthEndDateTime($startDateTime);

        $existingRides = $this->doctrine->getRepository(Ride::class)->findRidesByCycleInInterval($cityCycle, $startDateTime, $endDateTime);

        return count($existingRides) > 0;
    }
}
