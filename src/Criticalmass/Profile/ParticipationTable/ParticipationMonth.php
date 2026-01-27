<?php declare(strict_types=1);

namespace App\Criticalmass\Profile\ParticipationTable;

use App\Entity\Participation;
use Carbon\Carbon;

class ParticipationMonth implements \Countable, \Iterator
{
    /** @var int $year */
    protected $year;

    /** @var int $month */
    protected $month;

    /** @var array $dayList */
    protected $dayList = [];

    /** @var int $day */
    protected $day = 1;

    public function __construct(int $year, int $month)
    {
        $this->year = $year;
        $this->month = $month;
    }

    public function addParticipation(Participation $participation): ParticipationMonth
    {
        $ride = $participation->getRide();
        $dateTime = $ride->getDateTime();
        $day = (int) $dateTime->format('j');

        if (!isset($this->dayList[$day])) {
            $this->dayList[$day] = new ParticipationDay($this->year, $this->month, $day);
        }

        $this->dayList[$day]->addParticipation($participation);

        return $this;
    }

    public function count(): int
    {
        $counter = 0;

        foreach ($this->dayList as $participationDay) {
            $counter += count($participationDay);
        }

        return $counter;
    }

    public function getParticipationList(): array
    {
        return $this->dayList;
    }

    public function getMonth(): int
    {
        return $this->month;
    }

    public function __toString(): string
    {
        return (string) $this->month;
    }

    public function current(): ?Participation
    {
        if (array_key_exists($this->day, $this->dayList)) {
            return $this->dayList[$this->day];
        }

        return null;
    }

    public function next(): void
    {
        ++$this->day;
    }

    public function key(): int
    {
        return $this->day;
    }

    public function valid(): bool
    {
        $dateTimeSpec = '%d-%d-1';
        $dateTime = Carbon::parse(sprintf($dateTimeSpec, $this->year, $this->month));
        $maxDays = $dateTime->format('t');

        return (1 <= $this->day) && ($this->day <= $maxDays);
    }

    public function rewind(): void
    {
        $this->day = 1;
    }
}
