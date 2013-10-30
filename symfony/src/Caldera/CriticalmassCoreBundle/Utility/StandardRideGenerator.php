<?php

namespace Caldera\CriticalmassCoreBundle\Utility;

use \Caldera\CriticalmassCoreBundle\Entity\Ride;

class StandardRideGenerator
{
    private $doctrine;
    private $month;
    private $year;

    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine, $month, $year)
    {
        $this->doctrine = $doctrine;
        $this->month = $month;
        $this->year = $year;
    }

    public function searchDate($standardRide)
    {
        $timestamp = mktime(0, 0, 0, $this->month, 1, $this->year);

        if ($standardRide->getWeek() > 0)
        {
            $timestamp = mktime(0, 0, 0, $this->month, 1, $this->year);

            $dateFound = false;
            $startWeekday = date("N", $timestamp);
            $week = 0;

            while (!$dateFound)
            {
                $weekday = date("N", $timestamp);

                if ($startWeekday == $weekday)
                {
                    ++$week;
                }

                if (($week == $standardRide->getWeek()) && ($weekday == $standardRide->getWeekday()))
                {
                    $dateFound = true;
                }

                echo date("l, d.m.Y H:i:s", $timestamp)."<br />\n";

                $timestamp += 86400;
            }
        }
        else
        {
            $lastMonthDay = date("t", $timestamp);
            $timestamp = mktime(0, 0, 0, $this->month, $lastMonthDay, $this->year);

            $dateFound = false;

            while (!$dateFound)
            {
                $weekday = date("N", $timestamp);

                if ($weekday == $standardRide->getWeekday())
                {
                    $dateFound = true;
                }

                echo date("l, d.m.Y H:i:s", $timestamp)."<br />\n";

                $timestamp -= 86400;
            }
        }

        return $timestamp;
    }

    public function execute()
    {
        $standardRides = $this->doctrine->getRepository('CalderaCriticalmassCoreBundle:StandardRide')->findAll();

        foreach ($standardRides as $standardRide)
        {
            $ride = new Ride();

            $ride->setCity($standardRide->getCity());

            if ($standardRide->getTime())
            {
                $ride->setTime($standardRide->getTime());
            }

            if ($standardRide->getLocation())
            {
                $ride->setLocation($standardRide->getLocation());
                $ride->setLatitude($standardRide->getLatitude());
                $ride->setLongitude($standardRide->getLongitude());
            }

            $datetime = new \DateTime();
            $datetime->setTimestamp($this->searchDate($standardRide));
            $ride->setDate($datetime);

            $ride->setHasLocation(true);
            $ride->setHasTime(true);

            $manager = $this->doctrine->getManager();
            $manager->persist($ride);
            $manager->flush();
        }
    }
}