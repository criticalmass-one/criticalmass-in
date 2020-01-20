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
                if ($cycle->getDayOfWeek() === null || $cycle->getWeekOfMonth() === null) {
                    continue;
                }

                $monthStartDateTime = DateTimeUtil::getMonthStartDateTime($dateTime);

                $cityTimeZone = new \DateTimeZone($cycle->getCity()->getTimezone());
                $rideDateTime = DateTimeUtil::recreateAsTimeZone($monthStartDateTime, $cityTimeZone);

                $ride = $this->createRide($cycle, $rideDateTime);

                // yeah, first create ride and then check if it is matching the cycle range
                if (!DateTimeValidator::isValidRide($cycle, $ride)) {
                    continue;
                }

                if (!DateTimeValidator::isValidDateTime($cycle, $dateTime)) {
                    //continue;
                }

                $this->rideList[] = $ride;
            }
        }

        return $this;
    }

    protected function calculateDate(CityCycle $cityCycle, Ride $ride, \DateTime $startDateTime): Ride
    {
        $dayInterval = new \DateInterval('P1D');
        $weekInterval = new \DateInterval('P7D');

        $dateTime = clone $startDateTime;

        while ($dateTime->format('w') != $cityCycle->getDayOfWeek()) {
            $dateTime->add($dayInterval);
        }

        if ($cityCycle->getWeekOfMonth() > 0) {
            $weekOfMonth = $cityCycle->getWeekOfMonth();

            for ($i = 1; $i < $weekOfMonth; ++$i) {
                $dateTime->add($weekInterval);
            }
        } else {
            while ($dateTime->format('m') == $startDateTime->format('m')) {
                $dateTime->add($weekInterval);
            }

            $dateTime->sub($weekInterval);
        }

        $ride->setDateTime($dateTime);

        return $ride;
    }
}
