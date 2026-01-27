<?php declare(strict_types=1);

namespace App\Criticalmass\Profile\ParticipationTable;

use App\Entity\Participation;
use Carbon\Carbon;

class ParticipationTable implements \Countable, \Iterator
{
    /** @var array $yearList */
    protected $yearList = [];

    /** @var int $currentYear */
    protected $currentYear;

    public function __construct()
    {
        $this->currentYear = Carbon::now()->format('Y');
    }

    public function getYearList(): array
    {
        return $this->yearList;
    }

    public function addParticipation(Participation $participation): ParticipationTable
    {
        $ride = $participation->getRide();
        $year = (int) $ride->getDateTime()->format('Y');

        $this->createYearList($year);

        $this->yearList[$year]->addParticipation($participation);

        return $this;
    }

    protected function createYearList(int $fromYear): ParticipationTable
    {
        $this->currentYear = $fromYear;

        $untilYear = Carbon::now()->format('Y');

        for ($year = $fromYear; $year <= $untilYear; ++$year) {
            if (!array_key_exists($year, $this->yearList)) {
                $this->yearList[$year] = new ParticipationYear($year);
            }
        }

        return $this;
    }

    public function count(): int
    {
        $counter = 0;

        foreach ($this->yearList as $year) {
            $counter += count($year);
        }

        return $counter;
    }

    public function current(): ?ParticipationYear
    {
        return $this->yearList[$this->currentYear];
    }

    public function next(): void
    {
        --$this->currentYear;
    }

    public function key(): int
    {
        return (int) $this->currentYear;
    }

    public function valid(): bool
    {
        return array_key_exists($this->currentYear, $this->yearList);
    }

    public function rewind(): void
    {
        if (count($this->yearList) > 0) {
            $this->currentYear = max(array_keys($this->yearList));
        } else {
            $this->currentYear = Carbon::now()->format('Y');
        }
    }
}
