<?php declare(strict_types=1);

namespace App\Criticalmass\RideGenerator\RideGenerator;

use App\Criticalmass\Cycles\DateTimeValidator\DateTimeValidator;
use App\Criticalmass\RideGenerator\RideCalculator\RideCalculator;
use App\Criticalmass\RideGenerator\RideCalculator\RideCalculatorInterface;
use App\Criticalmass\Util\DateTimeUtil;
use App\Entity\City;
use App\Entity\CityCycle;
use App\Entity\Ride;

class RideGenerator extends AbstractRideGenerator
{
    public function execute(): RideGeneratorInterface
    {
        foreach ($this->cityList as $city) {
            foreach ($this->dateTimeList as $dateTime) {
                $startDateTime = DateTimeUtil::getMonthStartDateTime($dateTime);

                $cycles = $this->findCyclesForCity($city, $startDateTime);

                $createdRides = $this->processCityCycles($cycles, $startDateTime);

                $this->rideList = array_merge($this->rideList, $createdRides);
            }
        }

        return $this;
    }

    protected function findCyclesForCity(City $city, \DateTime $startDateTime): array
    {
        $endDateTime = DateTimeUtil::getMonthEndDateTime($startDateTime);

        return $this->doctrine->getRepository(CityCycle::class)->findByCity(
            $city,
            $startDateTime,
            $endDateTime
        );
    }

    protected function processCityCycles(array $cycles, \DateTime $startDateTime): array
    {
        $cycles = $this->removeCreatedCycles($cycles, $startDateTime);

        $rideList = [];

        foreach ($cycles as $cycle) {
            $ride = $this->getRideCalculatorForCycle($cycle)
                ->setCycle($cycle)
                ->setMonth((int)$startDateTime->format('m'))
                ->setYear((int)$startDateTime->format('Y'))
                ->execute();

            if ($ride && DateTimeValidator::isValidRide($cycle, $ride)) {
                $rideList[] = $ride;
            }
        }

        return $rideList;
    }

    protected function removeCreatedCycles(array $cycles, \DateTime $startDateTime): array
    {
        foreach ($cycles as $key => $cycle) {
            if ($this->hasRideAlreadyBeenCreated($cycle, $startDateTime)) {
                unset($cycles[$key]);
            }
        }

        return $cycles;
    }

    protected function hasRideAlreadyBeenCreated(CityCycle $cityCycle, \DateTime $startDateTime): bool
    {
        $endDateTime = DateTimeUtil::getMonthEndDateTime($startDateTime);

        $existingRides = $this->doctrine->getRepository(Ride::class)->findRidesByCycleInInterval($cityCycle, $startDateTime, $endDateTime);

        return count($existingRides) > 0;
    }

    protected function getRideCalculatorForCycle(CityCycle $cityCycle): RideCalculatorInterface
    {
        if (($rideCalculatorFqcn = $cityCycle->getRideCalculatorFqcn()) && class_exists($rideCalculatorFqcn)) {
            return new $rideCalculatorFqcn($this->rideNamerList);
        }

        return new RideCalculator($this->rideNamerList);
    }
}
