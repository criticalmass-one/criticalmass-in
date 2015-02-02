<?php

namespace Caldera\CriticalmassCoreBundle\Utility\StandardRideGenerator;

use Caldera\CriticalmassCoreBundle\Entity\City;
use PHPUnit_Framework_TestCase;

class StandardRideGeneratorTest extends PHPUnit_Framework_TestCase {
    public function test1()
    {
        $city = new City();
        $city->setIsStandardable(false);

        $srg = new StandardRideGenerator($city, 2015, 01);
        $ride = $srg->execute();

        $this->assertNull($ride);
    }

    public function test2()
    {
        $year = 2015;
        $month = 01;

        $city = new City();
        $city->setStandardDayOfWeek(5);
        $city->setStandardTime(new \DateTime("19:00:00"));
        $city->setStandardWeekOfMonth(5);
        $city->setStandardLatitude(9);
        $city->setStandardLongitude(53);
        $city->setStandardLocation('foo');
        $city->setIsStandardable(true);

        $srg = new StandardRideGenerator($city, $year, $month);
        $ride = $srg->execute();

        $this->assertNotNull($ride);
    }

    public function test3()
    {
        $year = 2015;
        $month = 01;

        $city = new City();
        $city->setStandardDayOfWeek(5);
        $city->setStandardTime(new \DateTime("19:00:00"));
        $city->setStandardWeekOfMonth(5);
        $city->setStandardLatitude(9);
        $city->setStandardLongitude(53);
        $city->setStandardLocation('foo');
        $city->setIsStandardable(true);

        $srg = new StandardRideGenerator($city, $year, $month);
        $ride = $srg->execute();

        $this->assertEquals($ride->getDateTime()->format('Y-m-d H-i-s'), '2015-01-30 19-00-00');
    }
} 