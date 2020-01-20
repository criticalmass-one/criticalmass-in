<?php declare(strict_types=1);

namespace App\Criticalmass\RideGenerator\RideCalculator;

use App\Criticalmass\Cycles\DateTimeValidator\DateTimeValidator;
use App\Criticalmass\Util\DateTimeUtil;
use App\Entity\CityCycle;
use App\Entity\Ride;

class FrankfurtRideCalculator extends RideCalculator
{
    public function execute(): RideCalculatorInterface
    {
        /** @var CityCycle $cycle */
        foreach ($this->cycleList as $cycle) {
            foreach ($this->dateTimeList as $dateTime) {
                $monthStartDateTime = DateTimeUtil::getMonthStartDateTime($dateTime);

                $cityTimeZone = new \DateTimeZone($cycle->getCity()->getTimezone());
                $rideDateTime = DateTimeUtil::recreateAsTimeZone($monthStartDateTime, $cityTimeZone);

                $ride = $this->createRide($cycle, $rideDateTime);

                // yeah, first create ride and then check if it is matching the cycle range
                if (!DateTimeValidator::isValidRide($cycle, $ride)) {
                    continue;
                }

                $this->rideList[] = $ride;
            }
        }

        return $this;
    }

    protected function calculateDate(CityCycle $cityCycle, Ride $ride, \DateTime $startDateTime): Ride
    {
        $dayInterval = new \DateInterval('P1D');
        $sundayToFridayInterval = new \DateInterval('P5D');

        $dateTime = clone $startDateTime;

        // first we look for the first sunday of the month
        while ($dateTime->format('w') != CityCycle::DAY_SUNDAY) {
            $dateTime->add($dayInterval);
        }

        // and then we add five days to get the friday ride date
        $dateTime->add($sundayToFridayInterval);

        $ride->setDateTime($dateTime);

        return $ride;
    }
}
