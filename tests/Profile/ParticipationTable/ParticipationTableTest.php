<?php declare(strict_types=1);

namespace Tests\Profile\ParticipationTable;

use App\Criticalmass\Profile\ParticipationTable\ParticipationTable;
use App\Criticalmass\Profile\ParticipationTable\ParticipationYear;
use App\Entity\Participation;
use App\Entity\Ride;
use PHPUnit\Framework\TestCase;

class ParticipationTableTest extends TestCase
{
    private function createParticipation(string $dateTime): Participation
    {
        $ride = $this->createMock(Ride::class);
        $ride->method('getDateTime')->willReturn(new \Carbon\Carbon($dateTime));

        $participation = $this->createMock(Participation::class);
        $participation->method('getRide')->willReturn($ride);

        return $participation;
    }

    public function testEmptyTableCountsZero(): void
    {
        $table = new ParticipationTable();

        $this->assertCount(0, $table);
    }

    public function testEmptyTableYearList(): void
    {
        $table = new ParticipationTable();

        $this->assertEmpty($table->getYearList());
    }

    public function testAddParticipation(): void
    {
        $table = new ParticipationTable();

        $result = $table->addParticipation($this->createParticipation('2024-06-28 19:00:00'));

        $this->assertSame($table, $result);
        $this->assertCount(1, $table);
    }

    public function testAddParticipationCreatesYear(): void
    {
        $table = new ParticipationTable();

        $table->addParticipation($this->createParticipation('2024-06-28 19:00:00'));

        $yearList = $table->getYearList();
        $this->assertArrayHasKey(2024, $yearList);
        $this->assertInstanceOf(ParticipationYear::class, $yearList[2024]);
    }

    public function testAddParticipationsInSameYear(): void
    {
        $table = new ParticipationTable();

        $table->addParticipation($this->createParticipation('2024-01-15 19:00:00'));
        $table->addParticipation($this->createParticipation('2024-06-28 19:00:00'));

        $this->assertCount(2, $table);

        // createYearList fills years from 2024 up to the current year
        $currentYear = (int) (new \Carbon\Carbon())->format('Y');
        $expectedYearCount = $currentYear - 2024 + 1;
        $this->assertCount($expectedYearCount, $table->getYearList());
    }

    public function testAddParticipationsInDifferentYears(): void
    {
        $table = new ParticipationTable();

        $table->addParticipation($this->createParticipation('2022-06-28 19:00:00'));
        $table->addParticipation($this->createParticipation('2024-06-28 19:00:00'));

        $this->assertCount(2, $table);
    }

    public function testCreatesIntermediateYears(): void
    {
        $table = new ParticipationTable();

        $table->addParticipation($this->createParticipation('2020-06-28 19:00:00'));

        $yearList = $table->getYearList();
        $currentYear = (int) (new \Carbon\Carbon())->format('Y');

        for ($year = 2020; $year <= $currentYear; $year++) {
            $this->assertArrayHasKey($year, $yearList, "Year $year should exist in the year list");
        }
    }

    public function testCountableInterface(): void
    {
        $this->assertInstanceOf(\Countable::class, new ParticipationTable());
    }

    public function testIteratorInterface(): void
    {
        $this->assertInstanceOf(\Iterator::class, new ParticipationTable());
    }

    public function testIteratorStartsFromMostRecentYear(): void
    {
        $table = new ParticipationTable();

        $table->addParticipation($this->createParticipation('2020-06-28 19:00:00'));
        $table->addParticipation($this->createParticipation('2024-06-28 19:00:00'));

        $table->rewind();
        $currentYear = (int) (new \Carbon\Carbon())->format('Y');
        $this->assertEquals($currentYear, $table->key());
    }

    public function testIteratorGoesBackwardsFromCurrentYear(): void
    {
        $table = new ParticipationTable();

        $table->addParticipation($this->createParticipation('2023-06-28 19:00:00'));

        $years = [];
        foreach ($table as $year => $participationYear) {
            $years[] = $year;
        }

        // Iterator starts from the most recent year (rewind sets to max) and decrements
        $currentYear = (int) (new \Carbon\Carbon())->format('Y');
        $this->assertEquals($currentYear, $years[0]);
        $this->assertEquals(2023, end($years));

        // Verify descending order
        for ($i = 1; $i < count($years); $i++) {
            $this->assertGreaterThan($years[$i], $years[$i - 1]);
        }
    }

    public function testCurrentReturnsParticipationYear(): void
    {
        $table = new ParticipationTable();

        $table->addParticipation($this->createParticipation('2024-06-28 19:00:00'));

        $table->rewind();
        $this->assertInstanceOf(ParticipationYear::class, $table->current());
    }

    public function testMultipleParticipationsAcrossYearsAndMonths(): void
    {
        $table = new ParticipationTable();

        $table->addParticipation($this->createParticipation('2023-01-15 19:00:00'));
        $table->addParticipation($this->createParticipation('2023-06-28 19:00:00'));
        $table->addParticipation($this->createParticipation('2024-03-10 19:00:00'));
        $table->addParticipation($this->createParticipation('2024-06-28 19:00:00'));
        $table->addParticipation($this->createParticipation('2024-12-20 19:00:00'));

        $this->assertCount(5, $table);
    }
}
