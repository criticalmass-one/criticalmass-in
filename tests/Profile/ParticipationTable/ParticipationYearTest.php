<?php declare(strict_types=1);

namespace Tests\Profile\ParticipationTable;

use App\Criticalmass\Profile\ParticipationTable\ParticipationMonth;
use App\Criticalmass\Profile\ParticipationTable\ParticipationYear;
use App\Entity\Participation;
use App\Entity\Ride;
use PHPUnit\Framework\TestCase;

class ParticipationYearTest extends TestCase
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
        $year = new ParticipationYear(2024);

        $this->assertEquals(2024, $year->getYear());
    }

    public function testInitializesAllTwelveMonths(): void
    {
        $year = new ParticipationYear(2024);

        $monthList = $year->getMonthList();

        $this->assertCount(12, $monthList);

        for ($month = 1; $month <= 12; $month++) {
            $this->assertArrayHasKey($month, $monthList);
            $this->assertInstanceOf(ParticipationMonth::class, $monthList[$month]);
        }
    }

    public function testEmptyYearCountsZero(): void
    {
        $year = new ParticipationYear(2024);

        $this->assertCount(0, $year);
    }

    public function testAddParticipation(): void
    {
        $year = new ParticipationYear(2024);

        $result = $year->addParticipation($this->createParticipation('2024-06-28 19:00:00'));

        $this->assertSame($year, $result);
        $this->assertCount(1, $year);
    }

    public function testAddParticipationsInDifferentMonths(): void
    {
        $year = new ParticipationYear(2024);

        $year->addParticipation($this->createParticipation('2024-01-15 19:00:00'));
        $year->addParticipation($this->createParticipation('2024-06-28 19:00:00'));
        $year->addParticipation($this->createParticipation('2024-12-20 19:00:00'));

        $this->assertCount(3, $year);
    }

    public function testAddMultipleParticipationsInSameMonth(): void
    {
        $year = new ParticipationYear(2024);

        $year->addParticipation($this->createParticipation('2024-06-15 19:00:00'));
        $year->addParticipation($this->createParticipation('2024-06-28 19:00:00'));

        $this->assertCount(2, $year);
    }

    public function testToString(): void
    {
        $year = new ParticipationYear(2024);

        $this->assertEquals('2024', (string) $year);
    }

    public function testIteratorCoversAllTwelveMonths(): void
    {
        $year = new ParticipationYear(2024);

        $monthCount = 0;
        foreach ($year as $month) {
            $monthCount++;
        }

        $this->assertEquals(12, $monthCount);
    }

    public function testIteratorStartsAtJanuary(): void
    {
        $year = new ParticipationYear(2024);

        $year->rewind();
        $this->assertEquals(1, $year->key());
    }

    public function testIteratorCurrentReturnsParticipationMonth(): void
    {
        $year = new ParticipationYear(2024);

        $year->rewind();
        $this->assertInstanceOf(ParticipationMonth::class, $year->current());
    }

    public function testCountableInterface(): void
    {
        $this->assertInstanceOf(\Countable::class, new ParticipationYear(2024));
    }

    public function testIteratorInterface(): void
    {
        $this->assertInstanceOf(\Iterator::class, new ParticipationYear(2024));
    }

    public function testParticipationRoutedToCorrectMonth(): void
    {
        $year = new ParticipationYear(2024);

        $year->addParticipation($this->createParticipation('2024-03-15 19:00:00'));

        $monthList = $year->getMonthList();
        $this->assertCount(1, $monthList[3]);
        $this->assertCount(0, $monthList[1]);
        $this->assertCount(0, $monthList[6]);
    }
}
