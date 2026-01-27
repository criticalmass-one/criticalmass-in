<?php declare(strict_types=1);

namespace Tests\Profile\ParticipationTable;

use App\Criticalmass\Profile\ParticipationTable\ParticipationDay;
use App\Criticalmass\Profile\ParticipationTable\ParticipationMonth;
use App\Entity\Participation;
use App\Entity\Ride;
use PHPUnit\Framework\TestCase;

class ParticipationMonthTest extends TestCase
{
    private function createParticipation(string $dateTime): Participation
    {
        $ride = $this->createMock(Ride::class);
        $ride->method('getDateTime')->willReturn(new \Carbon\Carbon($dateTime));

        $participation = $this->createMock(Participation::class);
        $participation->method('getRide')->willReturn($ride);

        return $participation;
    }

    public function testConstructor(): void
    {
        $month = new ParticipationMonth(2024, 6);

        $this->assertEquals(6, $month->getMonth());
    }

    public function testEmptyMonthCountsZero(): void
    {
        $month = new ParticipationMonth(2024, 6);

        $this->assertCount(0, $month);
    }

    public function testAddParticipation(): void
    {
        $month = new ParticipationMonth(2024, 6);

        $result = $month->addParticipation($this->createParticipation('2024-06-28 19:00:00'));

        $this->assertSame($month, $result);
        $this->assertCount(1, $month);
    }

    public function testAddMultipleParticipationsOnSameDay(): void
    {
        $month = new ParticipationMonth(2024, 6);

        $month->addParticipation($this->createParticipation('2024-06-28 19:00:00'));
        $month->addParticipation($this->createParticipation('2024-06-28 20:00:00'));

        $this->assertCount(2, $month);

        $dayList = $month->getParticipationList();
        $this->assertCount(1, $dayList);
        $this->assertArrayHasKey(28, $dayList);
    }

    public function testAddParticipationsOnDifferentDays(): void
    {
        $month = new ParticipationMonth(2024, 6);

        $month->addParticipation($this->createParticipation('2024-06-15 19:00:00'));
        $month->addParticipation($this->createParticipation('2024-06-28 19:00:00'));

        $this->assertCount(2, $month);

        $dayList = $month->getParticipationList();
        $this->assertCount(2, $dayList);
        $this->assertArrayHasKey(15, $dayList);
        $this->assertArrayHasKey(28, $dayList);
    }

    public function testDaysAreParticipationDayInstances(): void
    {
        $month = new ParticipationMonth(2024, 6);

        $month->addParticipation($this->createParticipation('2024-06-28 19:00:00'));

        $dayList = $month->getParticipationList();
        $this->assertInstanceOf(ParticipationDay::class, $dayList[28]);
    }

    public function testToString(): void
    {
        $month = new ParticipationMonth(2024, 6);

        $this->assertEquals('6', (string) $month);
    }

    public function testIteratorValidForJune(): void
    {
        $month = new ParticipationMonth(2024, 6);

        $month->rewind();
        $this->assertTrue($month->valid());
        $this->assertEquals(1, $month->key());
    }

    public function testIteratorCovers30DaysForJune(): void
    {
        $month = new ParticipationMonth(2024, 6);

        $dayCount = 0;
        foreach ($month as $day) {
            $dayCount++;
        }

        $this->assertEquals(30, $dayCount);
    }

    public function testIteratorCovers31DaysForJanuary(): void
    {
        $month = new ParticipationMonth(2024, 1);

        $dayCount = 0;
        foreach ($month as $day) {
            $dayCount++;
        }

        $this->assertEquals(31, $dayCount);
    }

    public function testIteratorCovers29DaysForFebruaryLeapYear(): void
    {
        $month = new ParticipationMonth(2024, 2);

        $dayCount = 0;
        foreach ($month as $day) {
            $dayCount++;
        }

        $this->assertEquals(29, $dayCount);
    }

    public function testIteratorCovers28DaysForFebruaryNonLeapYear(): void
    {
        $month = new ParticipationMonth(2023, 2);

        $dayCount = 0;
        foreach ($month as $day) {
            $dayCount++;
        }

        $this->assertEquals(28, $dayCount);
    }

    public function testCurrentReturnsNullForDayWithoutParticipation(): void
    {
        $month = new ParticipationMonth(2024, 6);

        $month->rewind();
        $this->assertNull($month->current());
    }

    public function testCountableInterface(): void
    {
        $this->assertInstanceOf(\Countable::class, new ParticipationMonth(2024, 6));
    }

    public function testIteratorInterface(): void
    {
        $this->assertInstanceOf(\Iterator::class, new ParticipationMonth(2024, 6));
    }
}
