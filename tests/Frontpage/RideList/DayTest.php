<?php declare(strict_types=1);

namespace Tests\Frontpage\RideList;

use App\Entity\City;
use App\Entity\Ride;
use App\Model\Frontpage\RideList\Day;
use PHPUnit\Framework\TestCase;

class DayTest extends TestCase
{
    private function createRide(string $cityName): Ride
    {
        $city = $this->createMock(City::class);
        $city->method('getCity')->willReturn($cityName);

        $ride = $this->createMock(Ride::class);
        $ride->method('getCity')->willReturn($city);

        return $ride;
    }

    public function testConstructor(): void
    {
        $dateTime = new \DateTime('2024-06-28');
        $day = new Day($dateTime);

        $this->assertSame($dateTime, $day->getDateTime());
    }

    public function testAddRide(): void
    {
        $day = new Day(new \DateTime('2024-06-28'));
        $ride = $this->createRide('Hamburg');

        $result = $day->addRide($ride);

        $this->assertSame($day, $result);
    }

    public function testIteratorWithNoRides(): void
    {
        $day = new Day(new \DateTime('2024-06-28'));

        $rides = [];
        foreach ($day as $ride) {
            $rides[] = $ride;
        }

        $this->assertEmpty($rides);
    }

    public function testIteratorWithRides(): void
    {
        $day = new Day(new \DateTime('2024-06-28'));
        $ride1 = $this->createRide('Hamburg');
        $ride2 = $this->createRide('Berlin');

        $day->addRide($ride1);
        $day->addRide($ride2);

        $rides = [];
        foreach ($day as $ride) {
            $rides[] = $ride;
        }

        $this->assertCount(2, $rides);
        $this->assertSame($ride1, $rides[0]);
        $this->assertSame($ride2, $rides[1]);
    }

    public function testSortByCity(): void
    {
        $day = new Day(new \DateTime('2024-06-28'));

        $day->addRide($this->createRide('München'));
        $day->addRide($this->createRide('Berlin'));
        $day->addRide($this->createRide('Hamburg'));

        $result = $day->sort();
        $this->assertSame($day, $result);

        $cities = [];
        foreach ($day as $ride) {
            $cities[] = $ride->getCity()->getCity();
        }

        $this->assertEquals(['Berlin', 'Hamburg', 'München'], $cities);
    }

    public function testSortWithSingleRide(): void
    {
        $day = new Day(new \DateTime('2024-06-28'));
        $day->addRide($this->createRide('Hamburg'));

        $day->sort();

        $rides = [];
        foreach ($day as $ride) {
            $rides[] = $ride;
        }

        $this->assertCount(1, $rides);
    }

    public function testSortWithEmptyList(): void
    {
        $day = new Day(new \DateTime('2024-06-28'));

        $day->sort();

        $rides = [];
        foreach ($day as $ride) {
            $rides[] = $ride;
        }

        $this->assertEmpty($rides);
    }

    public function testIteratorInterface(): void
    {
        $this->assertInstanceOf(\Iterator::class, new Day(new \DateTime()));
    }

    public function testValidReturnsFalseForEmptyDay(): void
    {
        $day = new Day(new \DateTime('2024-06-28'));

        $day->rewind();
        $this->assertFalse($day->valid());
    }

    public function testValidReturnsTrueWithRides(): void
    {
        $day = new Day(new \DateTime('2024-06-28'));
        $day->addRide($this->createRide('Hamburg'));

        $day->rewind();
        $this->assertTrue($day->valid());
    }
}
