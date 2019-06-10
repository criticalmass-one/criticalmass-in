<?php declare(strict_types=1);

namespace Tests\Geo\Entity;

use App\Criticalmass\Geo\Entity\Position;
use PHPUnit\Framework\TestCase;

class PositionTest extends TestCase
{
    public function testAltitudeAccuracy()
    {
        $position = new Position(57.5, 10.5);

        $position->setAltitude(42.3);

        $this->assertEquals(42.3, $position->getAltitude());
    }

    public function testHeading()
    {
        $position = new Position(57.5, 10.5);

        $position->setHeading(359.9);

        $this->assertEquals(359.9, $position->getHeading());
    }

    public function testDateTime()
    {
        $position = new Position(57.5, 10.5);

        $position->setDateTime(new \DateTime('2011-06-24 19:00:00'));

        $this->assertEquals(new \DateTime('2011-06-24 19:00:00'), $position->getDateTime());
    }

    public function testAccuracy()
    {
        $position = new Position(57.5, 10.5);

        $position->setAccuracy(42.3);

        $this->assertEquals(42.3, $position->getAccuracy());
    }

    public function testSpeed()
    {
        $position = new Position(57.5, 10.5);

        $position->setSpeed(42.3);

        $this->assertEquals(42.3, $position->getSpeed());
    }

    public function testAltitude()
    {
        $position = new Position(57.5, 10.5);

        $position->setAltitude(42.3);

        $this->assertEquals(42.3, $position->getAltitude());
    }

    public function testTimestamp()
    {
        $position = new Position(57.5, 10.5);

        $position->setTimestamp(42);

        $this->assertEquals(42, $position->getTimestamp());
    }
}
