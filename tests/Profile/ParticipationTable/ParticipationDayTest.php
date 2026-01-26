<?php declare(strict_types=1);

namespace Tests\Profile\ParticipationTable;

use App\Criticalmass\Profile\ParticipationTable\ParticipationDay;
use App\Entity\Participation;
use PHPUnit\Framework\TestCase;

class ParticipationDayTest extends TestCase
{
    public function testConstructor(): void
    {
        $day = new ParticipationDay(2024, 6, 28);

        $this->assertEquals(28, $day->getDay());
    }

    public function testEmptyDayCountsZero(): void
    {
        $day = new ParticipationDay(2024, 6, 28);

        $this->assertCount(0, $day);
    }

    public function testAddParticipation(): void
    {
        $day = new ParticipationDay(2024, 6, 28);
        $participation = $this->createMock(Participation::class);

        $result = $day->addParticipation($participation);

        $this->assertSame($day, $result);
        $this->assertCount(1, $day);
    }

    public function testAddMultipleParticipations(): void
    {
        $day = new ParticipationDay(2024, 6, 28);

        $day->addParticipation($this->createMock(Participation::class));
        $day->addParticipation($this->createMock(Participation::class));
        $day->addParticipation($this->createMock(Participation::class));

        $this->assertCount(3, $day);
    }

    public function testGetParticipationList(): void
    {
        $day = new ParticipationDay(2024, 6, 28);
        $p1 = $this->createMock(Participation::class);
        $p2 = $this->createMock(Participation::class);

        $day->addParticipation($p1);
        $day->addParticipation($p2);

        $list = $day->getParticipationList();

        $this->assertCount(2, $list);
        $this->assertSame($p1, $list[0]);
        $this->assertSame($p2, $list[1]);
    }

    public function testToString(): void
    {
        $day = new ParticipationDay(2024, 6, 28);

        $this->assertEquals('28', (string) $day);
    }

    public function testToStringWithSingleDigit(): void
    {
        $day = new ParticipationDay(2024, 1, 5);

        $this->assertEquals('5', (string) $day);
    }

    public function testCountableInterface(): void
    {
        $day = new ParticipationDay(2024, 6, 28);

        $this->assertInstanceOf(\Countable::class, $day);
    }

    public function testIteratorInterface(): void
    {
        $day = new ParticipationDay(2024, 6, 28);

        $this->assertInstanceOf(\Iterator::class, $day);
    }

    public function testIterateOverParticipations(): void
    {
        $day = new ParticipationDay(2024, 6, 28);
        $p1 = $this->createMock(Participation::class);
        $p2 = $this->createMock(Participation::class);

        $day->addParticipation($p1);
        $day->addParticipation($p2);

        $day->rewind();
        $this->assertSame($p1, $day->current());

        $day->next();
        $this->assertSame($p2, $day->current());
    }
}
