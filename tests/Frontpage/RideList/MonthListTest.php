<?php declare(strict_types=1);

namespace Tests\Frontpage\RideList;

use App\Entity\City;
use App\Entity\Ride;
use App\Model\Frontpage\RideList\Month;
use App\Model\Frontpage\RideList\MonthList;
use PHPUnit\Framework\TestCase;

class MonthListTest extends TestCase
{
    private function createRide(string $cityName, string $dateTime): Ride
    {
        $city = $this->createMock(City::class);
        $city->method('getCity')->willReturn($cityName);

        $ride = $this->createMock(Ride::class);
        $ride->method('getCity')->willReturn($city);
        $ride->method('getDateTime')->willReturn(new \Carbon\Carbon($dateTime));

        return $ride;
    }

    public function testEmptyMonthList(): void
    {
        $monthList = new MonthList();

        $this->assertEmpty($monthList->getMonthList());
    }

    public function testAddMonth(): void
    {
        $monthList = new MonthList();
        $month = new Month();

        $result = $monthList->addMonth($month);

        $this->assertSame($monthList, $result);
        $this->assertCount(1, $monthList->getMonthList());
    }

    public function testAddRide(): void
    {
        $monthList = new MonthList();

        $result = $monthList->addRide($this->createRide('Hamburg', '2024-06-28 19:00:00'));

        $this->assertSame($monthList, $result);

        $months = $monthList->getMonthList();
        $this->assertCount(1, $months);
        $this->assertArrayHasKey(6, $months);
    }

    public function testAddRidesInDifferentMonths(): void
    {
        $monthList = new MonthList();

        $monthList->addRide($this->createRide('Hamburg', '2024-01-15 19:00:00'));
        $monthList->addRide($this->createRide('Berlin', '2024-06-28 19:00:00'));
        $monthList->addRide($this->createRide('München', '2024-12-20 19:00:00'));

        $months = $monthList->getMonthList();
        $this->assertCount(3, $months);
        $this->assertArrayHasKey(1, $months);
        $this->assertArrayHasKey(6, $months);
        $this->assertArrayHasKey(12, $months);
    }

    public function testAddRidesInSameMonth(): void
    {
        $monthList = new MonthList();

        $monthList->addRide($this->createRide('Hamburg', '2024-06-15 19:00:00'));
        $monthList->addRide($this->createRide('Berlin', '2024-06-28 18:00:00'));

        $months = $monthList->getMonthList();
        $this->assertCount(1, $months);
        $this->assertArrayHasKey(6, $months);
    }

    public function testMonthsAreMonthInstances(): void
    {
        $monthList = new MonthList();
        $monthList->addRide($this->createRide('Hamburg', '2024-06-28 19:00:00'));

        $months = $monthList->getMonthList();
        $this->assertInstanceOf(Month::class, $months[6]);
    }

    public function testIteratorWithNoMonths(): void
    {
        $monthList = new MonthList();

        $months = [];
        foreach ($monthList as $month) {
            $months[] = $month;
        }

        $this->assertEmpty($months);
    }

    public function testIteratorWithMonths(): void
    {
        $monthList = new MonthList();

        $monthList->addRide($this->createRide('Hamburg', '2024-01-15 19:00:00'));
        $monthList->addRide($this->createRide('Berlin', '2024-06-28 19:00:00'));

        $months = [];
        foreach ($monthList as $key => $month) {
            $months[$key] = $month;
        }

        $this->assertCount(2, $months);
    }

    public function testIteratorInterface(): void
    {
        $this->assertInstanceOf(\Iterator::class, new MonthList());
    }

    public function testValidReturnsFalseForEmptyList(): void
    {
        $monthList = new MonthList();

        $monthList->rewind();
        $this->assertFalse($monthList->valid());
    }

    public function testValidReturnsTrueWithMonths(): void
    {
        $monthList = new MonthList();
        $monthList->addRide($this->createRide('Hamburg', '2024-06-28 19:00:00'));

        $monthList->rewind();
        $this->assertTrue($monthList->valid());
    }
}
