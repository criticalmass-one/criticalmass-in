<?php

namespace AppBundle\CityCycleRideGenerator;

use AppBundle\Entity\City;
use AppBundle\Entity\Ride;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;

class CityCycleRideGenerator
{
    /** @var int year */
    protected $year;

    /** @var int $month */
    protected $month;

    /** @var  \DateTime $dateTime */
    protected $dateTime;

    /** @var City $city */
    protected $city;

    /** @var array $rideList */
    protected $rideList;

    public function __construct(Doctrine $doctrine)
    {
        $this->year = $year;
        $this->month = $month;
        $this->city = $city;

        $this->ride = new Ride();
        $this->ride->setCity($city);
    }

    public function setCity(City $city): CityCycleRideGenerator
    {
        $this->city = $city;

        return $this;
    }

    public function setYear(int $year): CityCycleRideGenerator
    {
        $this->year = $year;

        return $this;
    }

    public function setMonth(int $month): CityCycleRideGenerator
    {
        $this->month = $month;

        return $this;
    }

    public function execute(): CityCycleRideGenerator
    {

        if ($this->city->getTimezone()) {
            $timezone = new \DateTimeZone($this->city->getTimezone());
        } else {
            $timezone = new \DateTimeZone('Europe/Berlin');
        }

        $this->ride->setDateTime(new \DateTime($this->year . '-' . $this->month . '-01 00:00:00', $timezone));

        $this->calculateDate();
        $this->calculateTime();
        $this->calculateLocation();

        return $this->ride;
    }

    protected function calculateDate()
    {
        $dateTime = $this->ride->getDateTime();

        $dayInterval = new \DateInterval('P1D');

        while ($dateTime->format('w') != $this->city->getStandardDayOfWeek()) {
            $dateTime->add($dayInterval);
        }

        if ($this->city->getStandardWeekOfMonth() > 0) {
            $weekInterval = new \DateInterval('P7D');

            $standardWeekOfMonth = $this->city->getStandardWeekOfMonth();

            for ($weekOfMonth = 1; $weekOfMonth < $standardWeekOfMonth; ++$weekOfMonth) {
                $dateTime->add($weekInterval);
            }
        } else {
            $weekInterval = new \DateInterval('P7D');

            while ($dateTime->format('m') == $this->month) {
                $dateTime->add($weekInterval);
            }

            $dateTime->sub($weekInterval);
        }

        $this->ride->setDateTime($dateTime);
    }

    protected function calculateTime()
    {
        $this->ride->setHasTime($this->city->getStandardTime() != null);

        if ($this->city->getStandardTime()) {
            $timeInterval = new \DateInterval('PT' . $this->city->getStandardTime()->format('H') . 'H' . $this->city->getStandardTime()->format('i') . 'M');
            $this->ride->setDateTime($this->ride->getDateTime()->add($timeInterval));
        }
    }

    protected function calculateLocation()
    {
        if ($this->city->getStandardLocation() && $this->city->getStandardLatitude() && $this->city->getStandardLongitude()) {
            $this->ride->setLocation($this->city->getStandardLocation());
            $this->ride->setLatitude($this->city->getStandardLatitude());
            $this->ride->setLongitude($this->city->getStandardLongitude());
            $this->ride->setHasLocation(true);
        } else {
            $this->ride->setHasLocation(false);
        }
    }

    public function isRideDuplicate(): bool
    {
        foreach ($this->city->getRides() as $ride) {
            if ($ride->isSameRide($this->ride)) {
                return true;
            }
        }

        return false;
    }
}