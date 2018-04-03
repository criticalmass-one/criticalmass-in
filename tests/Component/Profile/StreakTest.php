<?php declare(strict_types=1);

namespace Tests\Component\Profile;

use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Component\Profile\Streak\StreakCalculator;
use PHPUnit\Framework\TestCase;

class StreakTest extends TestCase
{
    protected function createRide(\DateTime $dateTime): Ride
    {
        $ride = new Ride();
        $ride->setDateTime($dateTime);

        return $ride;
    }

    public function testStreak1(): void
    {
        $streakCalculator = new StreakCalculator();

        $streakCalculator
            ->addRide($this->createRide(new \DateTime('2011-06-24 00:00:00')))
            ->addRide($this->createRide(new \DateTime('2011-08-26 00:00:00')))
            ->addRide($this->createRide(new \DateTime('2011-09-30 00:00:00')))
            ->addRide($this->createRide(new \DateTime('2011-10-28 00:00:00')))
            ->addRide($this->createRide(new \DateTime('2011-11-25 00:00:00')))
            ->addRide($this->createRide(new \DateTime('2011-12-30 00:00:00')));

        $streak = $streakCalculator->calculateLongestStreak();

        $this->assertEquals(new \DateTime('2011-08-01 00:00:00'), $streak->getStartDateTime());
        $this->assertEquals(new \DateTime('2011-12-01 00:00:00'), $streak->getEndDateTime());
        $this->assertEquals(5, count($streak->getRideList()));
    }
}
