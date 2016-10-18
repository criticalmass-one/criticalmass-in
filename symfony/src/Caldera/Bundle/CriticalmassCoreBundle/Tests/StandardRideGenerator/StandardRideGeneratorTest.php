<?php

namespace Caldera\CriticalmassStandardridesBundle\Tests\StandardRideGenerator;

use Caldera\CriticalmassCoreBundle\Entity\City;
use Caldera\CriticalmassStandardridesBundle\Utility\StandardRideGenerator\StandardRideGenerator;
use PHPUnit_Framework_TestCase;

class StandardRideGeneratorTest extends PHPUnit_Framework_TestCase
{

    protected $testCity;

    protected function setUp()
    {
        $this->testCity = new City();
        $this->testCity->setStandardDayOfWeek(5);
        $this->testCity->setStandardTime(new \DateTime("19:00:00"));
        $this->testCity->setStandardWeekOfMonth(0);
        $this->testCity->setStandardLatitude(9.9935);
        $this->testCity->setStandardLongitude(53.5506);
        $this->testCity->setStandardLocation('Rathausmarkt Hamburg');
        $this->testCity->setIsStandardable(true);
    }

    public function testNoStandardableRide()
    {
        $city = new City();
        $city->setIsStandardable(false);

        $srg = new StandardRideGenerator($city, 2015, 01);
        $ride = $srg->execute();

        $this->assertNull($ride);
    }

    public function testStandardableRide()
    {
        $srg = new StandardRideGenerator($this->testCity, 2015, 01);
        $ride = $srg->execute();

        $this->assertNotNull($ride);
    }

    public function testLocation()
    {
        $srg = new StandardRideGenerator($this->testCity, 2015, 01);
        $ride = $srg->execute();

        $this->assertEquals($ride->getLocation(), 'Rathausmarkt Hamburg');
        $this->assertEquals($ride->getLatitude(), 9.9935);
        $this->assertEquals($ride->getLongitude(), 53.5506);
    }

    public function testDateTime()
    {
        $srg = new StandardRideGenerator($this->testCity, 2015, 01);
        $ride = $srg->execute();

        $this->assertEquals($ride->getDateTime()->format('Y-m-d H-i-s'), '2015-01-30 19-00-00');

        $srg = new StandardRideGenerator($this->testCity, 2015, 02);
        $ride = $srg->execute();

        $this->assertEquals($ride->getDateTime()->format('Y-m-d H-i-s'), '2015-02-27 19-00-00');

        $srg = new StandardRideGenerator($this->testCity, 2015, 03);
        $ride = $srg->execute();

        $this->assertEquals($ride->getDateTime()->format('Y-m-d H-i-s'), '2015-03-27 19-00-00');
    }

    public function testDateTimeWeekOfMonth()
    {
        $this->testCity->setStandardWeekOfMonth(1);

        $srg = new StandardRideGenerator($this->testCity, 2015, 01);
        $ride = $srg->execute();

        $this->assertEquals($ride->getDateTime()->format('Y-m-d H-i-s'), '2015-01-02 19-00-00');

        $this->testCity->setStandardWeekOfMonth(2);

        $srg = new StandardRideGenerator($this->testCity, 2015, 01);
        $ride = $srg->execute();

        $this->assertEquals($ride->getDateTime()->format('Y-m-d H-i-s'), '2015-01-09 19-00-00');

        $this->testCity->setStandardWeekOfMonth(3);

        $srg = new StandardRideGenerator($this->testCity, 2015, 01);
        $ride = $srg->execute();

        $this->assertEquals($ride->getDateTime()->format('Y-m-d H-i-s'), '2015-01-16 19-00-00');

        $this->testCity->setStandardWeekOfMonth(4);

        $srg = new StandardRideGenerator($this->testCity, 2015, 01);
        $ride = $srg->execute();

        $this->assertEquals($ride->getDateTime()->format('Y-m-d H-i-s'), '2015-01-23 19-00-00');

        $this->testCity->setStandardWeekOfMonth(0);

        $srg = new StandardRideGenerator($this->testCity, 2015, 01);
        $ride = $srg->execute();

        $this->assertEquals($ride->getDateTime()->format('Y-m-d H-i-s'), '2015-01-30 19-00-00');
    }

    public function testDateTimeDayOfWeek()
    {
        $this->testCity->setStandardWeekOfMonth(1);

        $this->testCity->setStandardDayOfWeek(0);

        $srg = new StandardRideGenerator($this->testCity, 2015, 01);
        $ride = $srg->execute();

        $this->assertEquals($ride->getDateTime()->format('Y-m-d H-i-s'), '2015-01-04 19-00-00');

        $this->testCity->setStandardDayOfWeek(1);

        $srg = new StandardRideGenerator($this->testCity, 2015, 01);
        $ride = $srg->execute();

        $this->assertEquals($ride->getDateTime()->format('Y-m-d H-i-s'), '2015-01-05 19-00-00');

        $this->testCity->setStandardDayOfWeek(2);

        $srg = new StandardRideGenerator($this->testCity, 2015, 01);
        $ride = $srg->execute();

        $this->assertEquals($ride->getDateTime()->format('Y-m-d H-i-s'), '2015-01-06 19-00-00');

        $this->testCity->setStandardDayOfWeek(3);

        $srg = new StandardRideGenerator($this->testCity, 2015, 01);
        $ride = $srg->execute();

        $this->assertEquals($ride->getDateTime()->format('Y-m-d H-i-s'), '2015-01-07 19-00-00');

        $this->testCity->setStandardDayOfWeek(4);

        $srg = new StandardRideGenerator($this->testCity, 2015, 01);
        $ride = $srg->execute();

        $this->assertEquals($ride->getDateTime()->format('Y-m-d H-i-s'), '2015-01-01 19-00-00');

        $this->testCity->setStandardDayOfWeek(5);

        $srg = new StandardRideGenerator($this->testCity, 2015, 01);
        $ride = $srg->execute();

        $this->assertEquals($ride->getDateTime()->format('Y-m-d H-i-s'), '2015-01-02 19-00-00');

        $this->testCity->setStandardDayOfWeek(6);

        $srg = new StandardRideGenerator($this->testCity, 2015, 01);
        $ride = $srg->execute();

        $this->assertEquals($ride->getDateTime()->format('Y-m-d H-i-s'), '2015-01-03 19-00-00');
    }
} 