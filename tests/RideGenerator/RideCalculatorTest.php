<?php declare(strict_types=1);

namespace Tests\Component\Util\RideGenerator;

use AppBundle\Entity\City;
use AppBundle\Entity\CityCycle;
use AppBundle\Entity\Ride;
use AppBundle\Criticalmass\RideGenerator\RideCalculator\RideCalculator;
use AppBundle\Criticalmass\RideGenerator\RideCalculator\RideCalculatorInterface;
use PHPUnit\Framework\TestCase;

class RideCalculatorTest extends TestCase
{
    protected function getRideCalculator(): RideCalculatorInterface
    {
        return new RideCalculator();
    }

    protected function createCycle(): CityCycle
    {
        $city = new City();
        $city
            ->setCity('Hamburg')
            ->setTimezone('Europe/Berlin')
        ;

        $cityCycle = new CityCycle();
        $cityCycle
            ->setWeekOfMonth(CityCycle::WEEK_LAST)
            ->setDayOfWeek(CityCycle::DAY_FRIDAY)
            ->setTime(new \DateTime('18:00:00'))
            ->setLocation('Stadtpark Hamburg')
            ->setLatitude(53.596812)
            ->setLongitude(10.011008)
            ->setCity($city)
        ;

        return $cityCycle;
    }

    public function testCalculatedRides(): void
    {
        $rideList = $this->getRideCalculator()
            ->setYear(2018)
            ->setMonth(9)
            ->addCycle($this->createCycle())
            ->execute()
            ->getRideList()
        ;

        $this->assertEquals(1, count($rideList));
    }

    public function testLocation(): void
    {
        $rideList = $this->getRideCalculator()
            ->setYear(2018)
            ->setMonth(9)
            ->addCycle($this->createCycle())
            ->execute()
            ->getRideList()
        ;

        /** @var Ride $ride */
        $ride = array_pop($rideList);

        $this->assertEquals('Stadtpark Hamburg', $ride->getLocation());
        $this->assertEquals(53.596812, $ride->getLatitude());
        $this->assertEquals(10.011008, $ride->getLongitude());
        $this->assertTrue($ride->getHasLocation());
    }

    public function testTimezone(): void
    {
        $rideList = $this->getRideCalculator()
            ->setYear(2018)
            ->setMonth(9)
            ->addCycle($this->createCycle())
            ->execute()
            ->getRideList()
        ;

        /** @var Ride $ride */
        $ride = array_pop($rideList);

        $this->assertEquals(new \DateTimeZone('Europe/Berlin'), $ride->getDateTime()->getTimezone());
    }

    public function testTime(): void
    {
        $rideList = $this->getRideCalculator()
            ->setYear(2018)
            ->setMonth(9)
            ->addCycle($this->createCycle())
            ->execute()
            ->getRideList()
        ;

        $utc = new \DateTimeZone('UTC');

        /** @var Ride $ride */
        $ride = array_pop($rideList);

        $this->assertEquals(new \DateTime('2018-09-28 18:00:00', $utc), $ride->getDateTime()->setTimezone($utc));
        $this->assertTrue($ride->getHasTime());
    }

    public function testDaylightSavingTime(): void
    {
        $rideList = $this->getRideCalculator()
            ->setYear(2018)
            ->setMonth(2)
            ->addCycle($this->createCycle())
            ->execute()
            ->getRideList()
        ;

        $europeBerlin = new \DateTimeZone('Europe/Berlin');

        /** @var Ride $ride */
        $ride = array_pop($rideList);

        $this->assertEquals(new \DateTime('2018-02-23 20:00:00', $europeBerlin), $ride->getDateTime());
        $this->assertTrue($ride->getHasTime());
    }
}
