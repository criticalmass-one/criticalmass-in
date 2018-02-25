<?php declare(strict_types=1);

namespace Criticalmass\Component\RideGenerator\Tests;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\CityCycle;
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

    public function testRideCalculator1(): void
    {
        $this->getRideCalculator()
            ->addCycle($this->createCycle())
            ->execute()
            ->getRideList()
        ;
    }
}
