<?php declare(strict_types=1);

namespace App\Criticalmass\Profile\ParticipationTable;

use App\Entity\Participation;

class ParticipationYear implements \Countable, \Iterator, \Stringable
{
    /** @var array $monthList */
    protected $monthList;

    protected $currentMonth = 1;

    public function __construct(protected int $year)
    {
        $this->initMonthList();
    }

    protected function initMonthList(): ParticipationYear
    {
        for ($month = 1; $month <= 12; ++$month) {
            $this->monthList[$month] = new ParticipationMonth($this->year, $month);
        }

        return $this;
    }

    public function addParticipation(Participation $participation): ParticipationYear
    {
        $ride = $participation->getRide();
        $dateTime = $ride->getDateTime();
        $month = (int) $dateTime->format('n');

        $this->monthList[$month]->addParticipation($participation);

        return $this;
    }

    public function count(): int
    {
        $counter = 0;

        for ($month = 1; $month <= 12; ++$month) {
            $counter += is_countable($this->monthList[$month]) ? count($this->monthList[$month]) : 0;
        }

        return $counter;
    }

    public function getMonthList(): array
    {
        return $this->monthList;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function __toString(): string
    {
        return (string) $this->year;
    }

    public function current(): ParticipationMonth
    {
        return $this->monthList[$this->currentMonth];
    }

    public function next(): void
    {
        ++$this->currentMonth;
    }

    public function key(): int
    {
        return $this->currentMonth;
    }

    public function valid(): bool
    {
        return (1 <= $this->currentMonth) && ($this->currentMonth <= 12);
    }

    public function rewind(): void
    {
        $this->currentMonth = 1;
    }
}
