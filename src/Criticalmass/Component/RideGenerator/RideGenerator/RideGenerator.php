<?php

namespace Criticalmass\Component\RideGenerator\RideGenerator;

use Criticalmass\Bundle\AppBundle\Entity\CityCycle;
use Criticalmass\Bundle\AppBundle\Entity\Ride;

class RideGenerator extends AbstractRideGenerator
{
    public function execute(): RideGeneratorInterface
    {
        $this->rideList = [];

        $this->startDateTime = new \DateTime(sprintf('%d-%d-01 00:00:00', $this->year, $this->month));
        $this->endDateTime = new \DateTime(sprintf('%d-%d-%d 23:59:59', $this->year, $this->month, $this->startDateTime->format('t')));

        $cycles = $this->findCylces();

        /** @var CityCycle $cycle */
        foreach ($cycles as $cycle) {
            if ($this->wasRideCreated($cycle)) {
                continue;
            }

            if (!$cycle->getCity()->getTimezone()) {
                continue;
            }

            $ride = new Ride();
            $ride
                ->setCity($this->city)
                ->setCycle($cycle)
            ;

            $ride = $this->calculateDate($cycle, $ride);
            $ride = $this->calculateTime($cycle, $ride);
            $ride = $this->setupLocation($cycle, $ride);

            $this->rideList[] = $ride;
        }

        return $this;
    }

    protected function findCylces(): array
    {
        return $this->doctrine->getRepository(CityCycle::class)->findByCity($this->city, $this->startDateTime, $this->endDateTime);
    }

    protected function calculateDate(CityCycle $cityCycle, Ride $ride): Ride
    {
        $dateTime = clone $this->startDateTime;

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
        $timezone = new \DateTimeZone($cityCycle->getCity()->getTimezone());

        $time->setTimezone(new \DateTimeZone('UTC'));

        $intervalSpec = sprintf('PT%dH%dM', $time->format('H'), $time->format('i'));
        $timeInterval = new \DateInterval($intervalSpec);

        $dateTimeSpec = sprintf('%d-%d-%d 00:00:00', $ride->getDateTime()->format('Y'), $ride->getDateTime()->format('m'), $ride->getDateTime()->format('d'));
        $rideDateTime = new \DateTime($dateTimeSpec);
        $rideDateTime->add($timeInterval);

        $rideDateTime->setTimezone($timezone);

        $ride
            ->setDateTime($rideDateTime)
            ->setHasTime(true)
        ;

        return $ride;
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

    public function wasRideCreated(CityCycle $cityCycle): bool
    {
        $existingRides = $this->doctrine->getRepository(Ride::class)->findRidesByCycleInInterval($cityCycle, $this->startDateTime, $this->endDateTime);

        return count($existingRides) > 0;
    }
}
