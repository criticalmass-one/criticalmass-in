<?php declare(strict_types=1);

namespace Tests\Frontpage\RideList;

use App\Entity\City;
use App\Entity\Ride;
use App\Model\Frontpage\RideList\Day;
use App\Model\Frontpage\RideList\Month;
use PHPUnit\Framework\TestCase;

class MonthTest extends TestCase
{
    private function createRide(string $cityName, string $dateTime): Ride
    {
        $city = $this->createMock(City::class);
        $city->method('getCity')->willReturn($cityName);

        $ride = $this->createMock(Ride::class);
        $ride->method('getCity')->willReturn($city);
        $ride->method('getDateTime')->willReturn(new \DateTime($dateTime));

        return $ride;
    }

    public function testConstructor(): void
    {
        $month = new Month();

        $this->assertInstanceOf(\DateTime::class, $month->getDateTime());
    }

    public function testAddRide(): void
    {
        $month = new Month();

        $result = $month->addRide($this->createRide('Hamburg', '2024-06-28 19:00:00'));

        $this->assertSame($month, $result);
    }

    public function testRidesGroupedByDay(): void
    {
        $month = new Month();

        $month->addRide($this->createRide('Hamburg', '2024-06-15 19:00:00'));
        $month->addRide($this->createRide('Berlin', '2024-06-15 18:00:00'));
        $month->addRide($this->createRide('München', '2024-06-28 19:00:00'));

        $days = [];
        foreach ($month as $dayKey => $day) {
            $days[$dayKey] = $day;
        }

        $this->assertCount(2, $days);
        $this->assertArrayHasKey(15, $days);
        $this->assertArrayHasKey(28, $days);
    }

    public function testDaysAreDayInstances(): void
    {
        $month = new Month();
        $month->addRide($this->createRide('Hamburg', '2024-06-28 19:00:00'));

        $month->rewind();
        $this->assertInstanceOf(Day::class, $month->current());
    }

    public function testEmptyMonth(): void
    {
        $month = new Month();

        $days = [];
        foreach ($month as $day) {
            $days[] = $day;
        }

        $this->assertEmpty($days);
    }

    public function testSort(): void
    {
        $month = new Month();

        $month->addRide($this->createRide('München', '2024-06-28 19:00:00'));
        $month->addRide($this->createRide('Berlin', '2024-06-28 18:00:00'));
        $month->addRide($this->createRide('Hamburg', '2024-06-28 19:30:00'));

        $result = $month->sort();
        $this->assertSame($month, $result);

        $month->rewind();
        $day = $month->current();

        $cities = [];
        foreach ($day as $ride) {
            $cities[] = $ride->getCity()->getCity();
        }

        $this->assertEquals(['Berlin', 'Hamburg', 'München'], $cities);
    }

    public function testIteratorInterface(): void
    {
        $this->assertInstanceOf(\Iterator::class, new Month());
    }

    public function testMultipleRidesOnSameDay(): void
    {
        $month = new Month();

        $month->addRide($this->createRide('Hamburg', '2024-06-28 19:00:00'));
        $month->addRide($this->createRide('Berlin', '2024-06-28 18:00:00'));
        $month->addRide($this->createRide('München', '2024-06-28 19:30:00'));

        $month->rewind();
        $day = $month->current();

        $rideCount = 0;
        foreach ($day as $ride) {
            $rideCount++;
        }

        $this->assertEquals(3, $rideCount);
    }
}
