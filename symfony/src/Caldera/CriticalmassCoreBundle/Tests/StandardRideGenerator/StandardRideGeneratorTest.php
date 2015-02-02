<?php

namespace Caldera\CriticalmassCoreBundle\Utility\StandardRideGenerator;

use Caldera\CriticalmassCoreBundle\Entity\City;
use PHPUnit_Framework_TestCase;

class StandardRideGeneratorTest extends PHPUnit_Framework_TestCase {

    protected $testCity;

    protected function setUp()
    {
        $this->testCity = new City();
        $this->testCity->setStandardDayOfWeek(5);
        $this->testCity->setStandardTime(new \DateTime("19:00:00"));
        $this->testCity->setStandardWeekOfMonth(5);
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

    public function testDateTime()
    {
        $srg = new StandardRideGenerator($this->testCity, 2015, 01);
        $ride = $srg->execute();

        $this->assertEquals($ride->getDateTime()->format('Y-m-d H-i-s'), '2015-01-30 19-00-00');
    }

    public function testLocation()
    {
        $srg = new StandardRideGenerator($this->testCity, 2015, 01);
        $ride = $srg->execute();

        $this->assertEquals($ride->getLocation(), 'Rathausmarkt Hamburg');
        $this->assertEquals($ride->getLatitude(), 9.9935);
        $this->assertEquals($ride->getLongitude(), 53.5506);
    }
} 