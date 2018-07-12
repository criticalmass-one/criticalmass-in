<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\RideGenerator\RideCalculator;

use AppBundle\Entity\CityCycle;
use AppBundle\Entity\Ride;
use AppBundle\Criticalmass\RideGenerator\Exception\InvalidMonthException;
use AppBundle\Criticalmass\RideGenerator\Exception\InvalidYearException;

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

        /** @var CityCycle $cycle */
        foreach ($this->cycleList as $cycle) {
            $ride = $this->createRide($cycle);

            // yeah, first create ride and then check if it is matching the cycle range
            if (!$cycle->isValid($ride->getDateTime())) {
                continue;
            }

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
        $ride = $this->generateTitle($cycle, $ride);

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

        $timezone = $this->timezone ?? $this->getCityTimeZone($cityCycle);

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

    protected function generateTitle(CityCycle $cityCycle, Ride $ride): Ride
    {
        if (!$ride->getDateTime()) {
            return $ride;
        }

        $title = sprintf('%s %s', $cityCycle->getCity()->getTitle(), $ride->getDateTime()->format('d.m.Y'));

        $ride->setTitle($title);

        return $ride;
    }
}
