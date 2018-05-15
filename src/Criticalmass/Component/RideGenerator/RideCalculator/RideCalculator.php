<?php declare(strict_types=1);

namespace Criticalmass\Component\RideGenerator\RideCalculator;

use Criticalmass\Bundle\AppBundle\Entity\CityCycle;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Component\RideGenerator\Exception\InvalidMonthException;
use Criticalmass\Component\RideGenerator\Exception\InvalidYearException;

class RideCalculator extends AbstractRideCalculator
{
    public function execute(): RideCalculatorInterface
    {
        if (!$this->year) {
            throw new InvalidYearException();
        }

        if (!$this->month) {
            throw new InvalidMonthException();
        }

        foreach ($this->cycleList as $cycle) {
            $ride = $this->createRide($cycle);

            $this->rideList[] = $ride;
        }

        return $this;
    }

    protected function createRide(CityCycle $cycle): Ride
    {
        $ride = new Ride();
        $ride
            ->setCity($cycle->getCity())
            ->setCycle($cycle);

        $ride = $this->calculateDate($cycle, $ride);
        $ride = $this->calculateTime($cycle, $ride);
        $ride = $this->setupLocation($cycle, $ride);

        return $ride;
    }

    protected function calculateDate(CityCycle $cityCycle, Ride $ride): Ride
    {
        $dateTimeSpec = sprintf('%d-%d-01 00:00:00', $this->year, $this->month);
        $dateTime = new \DateTime($dateTimeSpec);

        $dayInterval = new \DateInterval('P1D');

        while ($dateTime->format('w') != $cityCycle->getDayOfWeek()) {
            $dateTime->add($dayInterval);
        }

        if ($cityCycle->getWeekOfMonth() > 0) {
            $weekInterval = new \DateInterval('P7D');

            $weekOfMonth = $cityCycle->getWeekOfMonth();

            for ($i = 1; $i < $weekOfMonth; ++$i) {
                $dateTime->add($weekInterval);
            }
        } else {
            $weekInterval = new \DateInterval('P7D');

            while ($dateTime->format('m') == $this->month) {
                $dateTime->add($weekInterval);
            }

            $dateTime->sub($weekInterval);
        }

        $ride->setDateTime($dateTime);

        return $ride;
    }

    protected function calculateTime(CityCycle $cityCycle, Ride $ride): Ride
    {
        $time = $cityCycle->getTime();

        $timezone = $this->getCityTimeZone($cityCycle);
        
        $time->setTimezone($timezone);

        $intervalSpec = sprintf('PT%dH%dM', $time->format('H'), $time->format('i'));
        $timeInterval = new \DateInterval($intervalSpec);

        $dateTimeSpec = sprintf('%d-%d-%d 00:00:00', $ride->getDateTime()->format('Y'), $ride->getDateTime()->format('m'), $ride->getDateTime()->format('d'));
        $rideDateTime = new \DateTime($dateTimeSpec, $timezone);
        $rideDateTime->add($timeInterval);

        $ride
            ->setDateTime($rideDateTime)
            ->setHasTime(true);

        return $ride;
    }

    protected function getCityTimeZone(CityCycle $cityCycle): \DateTimeZone
    {
        if ($timezoneSpec = $cityCycle->getCity()->getTimezone()) {
            $timezone = new \DateTimeZone($timezoneSpec);
        } else {
            $timezone = new \DateTimeZone('Europe/Berlin');
        }

        return $timezone;
    }

    protected function setupLocation(CityCycle $cityCycle, Ride $ride): Ride
    {
        $ride
            ->setLatitude($cityCycle->getLatitude())
            ->setLongitude($cityCycle->getLongitude())
            ->setLocation($cityCycle->getLocation())
            ->setHasLocation(true)
        ;

        return $ride;
    }
}
