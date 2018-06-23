<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Profile\ParticipationTable;

use AppBundle\Entity\Participation;

class ParticipationMonth implements \Countable, \Iterator
{
    /** @var int $year */
    protected $year;

    /** @var int $month */
    protected $month;

    /** @var array $participationList */
    protected $participationList = [];

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
        $day = (int)$dateTime->format('j');

        $this->participationList[$day] = $participation;

        return $this;
    }

    public function count(): int
    {
        return count($this->participationList);
    }

    public function getParticipationList(): array
    {
        return $this->participationList;
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
        if (array_key_exists($this->day, $this->participationList)) {
            return $this->participationList[$this->day];
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
        $dateTime = new \DateTime(sprintf($dateTimeSpec, $this->year, $this->month));
        $maxDays = $dateTime->format('t');

        return (1 <= $this->day) && ($this->day <= $maxDays);
    }

    public function rewind(): void
    {
        $this->day = 1;
    }
}
