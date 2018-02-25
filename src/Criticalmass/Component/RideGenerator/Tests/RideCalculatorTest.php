<?php declare(strict_types=1);

namespace Criticalmass\Component\RideGenerator\Tests;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\CityCycle;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Component\RideGenerator\RideCalculator\RideCalculator;
use Criticalmass\Component\RideGenerator\RideCalculator\RideCalculatorInterface;
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
            ->setTime(new \DateTime('19:00:00'))
            ->setLocation('Stadtpark Hamburg')
            ->setLatitude(53.596812)
            ->setLongitude(10.011008)
            ->setCity($city)
        ;

        return $cityCycle;
    }

    public function testRideCalculatorCalculatedRides(): void
    {
        $rideList = $this->getRideCalculator()
            ->addCycle($this->createCycle())
            ->execute()
            ->getRideList()
        ;

        $this->assertEquals(1, count($rideList));
    }

    public function testRideCalculatorLocation(): void
    {
        $rideList = $this->getRideCalculator()
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
}
