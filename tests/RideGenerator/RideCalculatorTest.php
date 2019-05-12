<?php declare(strict_types=1);

namespace Tests\RideGenerator;

use App\Entity\City;
use App\Entity\CityCycle;
use App\Entity\Ride;
use App\Criticalmass\RideGenerator\RideCalculator\RideCalculator;
use App\Criticalmass\RideGenerator\RideCalculator\RideCalculatorInterface;
use PHPUnit\Framework\TestCase;

class RideCalculatorTest extends TestCase
{
    protected function getRideCalculator(): RideCalculatorInterface
    {
        return new RideCalculator();
    }

    protected function createLondonCycle(): CityCycle
    {
        $city = new City();
        $city
            ->setCity('London')
            ->setTimezone('Europe/London');

        $cityCycle = new CityCycle();
        $cityCycle
            ->setWeekOfMonth(CityCycle::WEEK_LAST)
            ->setDayOfWeek(CityCycle::DAY_FRIDAY)
            ->setTime(new \DateTime('18:00:00'), new \DateTimeZone('Europe/London'))
            ->setLocation('Southbank under Waterloo Bridge')
            ->setLatitude(51.507320112865)
            ->setLongitude(-0.11578559875488)
            ->setCity($city);

        return $cityCycle;
    }

    protected function createHamburgCycle(): CityCycle
    {
        $city = new City();
        $city
            ->setCity('Hamburg')
            ->setTimezone('Europe/Berlin');

        $cityCycle = new CityCycle();
        $cityCycle
            ->setWeekOfMonth(CityCycle::WEEK_LAST)
            ->setDayOfWeek(CityCycle::DAY_FRIDAY)
            ->setTime(new \DateTime('19:00:00'))
            ->setLocation('Stadtpark Hamburg')
            ->setLatitude(53.596812)
            ->setLongitude(10.011008)
            ->setCity($city);

        return $cityCycle;
    }

    protected function createHalleCycle(): CityCycle
    {
        $city = new City();
        $city
            ->setCity('Halle')
            ->setTimezone('Europe/Berlin');

        $cityCycle = new CityCycle();
        $cityCycle
            ->setWeekOfMonth(CityCycle::WEEK_FIRST)
            ->setDayOfWeek(CityCycle::DAY_FRIDAY)
            ->setTime(new \DateTime('18:00:00'))
            ->setLocation('August-Bebel-Platz')
            ->setLatitude(51.491664696772)
            ->setLongitude(11.96897149086)
            ->setCity($city)
            ->setValidFrom(new \DateTime('2018-03-30'));

        return $cityCycle;
    }

    public function testCalculatedRides(): void
    {
        $rideList = $this->getRideCalculator()
            ->setYear(2018)
            ->setMonth(9)
            ->addCycle($this->createHamburgCycle())
            ->execute()
            ->getRideList();

        $this->assertEquals(1, count($rideList));
    }

    public function testLocation(): void
    {
        $rideList = $this->getRideCalculator()
            ->setYear(2018)
            ->setMonth(9)
            ->addCycle($this->createHamburgCycle())
            ->execute()
            ->getRideList();

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
            ->addCycle($this->createHamburgCycle())
            ->execute()
            ->getRideList();

        /** @var Ride $ride */
        $ride = array_pop($rideList);

        $this->assertEquals(new \DateTimeZone('Europe/Berlin'), $ride->getDateTime()->getTimezone());
    }

    public function testLondon(): void
    {
        $rideList = $this->getRideCalculator()
            ->setYear(2018)
            ->setMonth(9)
            ->addCycle($this->createLondonCycle())
            ->execute()
            ->getRideList();

        /** @var Ride $ride */
        $ride = array_pop($rideList);

        $europeLondon = new \DateTimeZone('Europe/London');
        $europeBerlin = new \DateTimeZone('Europe/Berlin');

        $this->assertEquals($europeLondon, $ride->getDateTime()->getTimezone());
        $this->assertEquals((new \DateTime('2018-09-28 18:00:00', $europeLondon))->format('Y-m-d H:i:s'), $ride->getDateTime()->format('Y-m-d H:i:s'));
    }

    public function testTime(): void
    {
        $rideList = $this->getRideCalculator()
            ->setYear(2018)
            ->setMonth(9)
            ->addCycle($this->createHamburgCycle())
            ->execute()
            ->getRideList();

        $utc = new \DateTimeZone('UTC');
        $europeBerlin = new \DateTimeZone('Europe/Berlin');

        /** @var Ride $ride */
        $ride = array_pop($rideList);

        $this->assertEquals(new \DateTime('2018-09-28 17:00:00', $utc), $ride->getDateTime());
        $this->assertEquals($europeBerlin, $ride->getDateTime()->getTimezone());
        $this->assertTrue($ride->getHasTime());
    }

    public function testDaylightSavingTime(): void
    {
        $rideList = $this->getRideCalculator()
            ->setYear(2018)
            ->setMonth(2)
            ->addCycle($this->createHamburgCycle())
            ->execute()
            ->getRideList();

        $europeBerlin = new \DateTimeZone('Europe/Berlin');

        /** @var Ride $ride */
        $ride = array_pop($rideList);

        $this->assertEquals(new \DateTime('2018-02-23 19:00:00', $europeBerlin), $ride->getDateTime());
        $this->assertEquals($europeBerlin, $ride->getDateTime()->getTimezone());
        $this->assertTrue($ride->getHasTime());
    }
}
