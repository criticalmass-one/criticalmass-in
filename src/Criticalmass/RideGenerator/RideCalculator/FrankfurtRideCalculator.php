<?php declare(strict_types=1);

namespace App\Criticalmass\RideGenerator\RideCalculator;

use App\Criticalmass\Cycles\DateTimeValidator\DateTimeValidator;
use App\Criticalmass\Util\DateTimeUtil;
use App\Entity\CityCycle;
use App\Entity\Ride;

class FrankfurtRideCalculator extends RideCalculator
{
    public function execute(): Ride
    {
        $dateTimeSpec = sprintf('%d-%d-01', $this->year, $this->month);
        $dateTime = new \DateTime($dateTimeSpec);

        $cityTimeZone = new \DateTimeZone($this->cycle->getCity()->getTimezone());
        $rideDateTime = DateTimeUtil::recreateAsTimeZone($dateTime, $cityTimeZone);

        $ride = $this->createRide($this->cycle, $rideDateTime);

        // yeah, first create ride and then check if it is matching the cycle range
        if ($ride && DateTimeValidator::isValidRide($this->cycle, $ride)) {
            return $ride;
        }

        return null;
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
