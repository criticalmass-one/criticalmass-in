<?php declare(strict_types=1);

namespace Criticalmass\Component\Profile\ParticipationTable;

class ParticipationMonth implements \Countable
{
    protected $year;
    protected $month;

    protected $rideList;

    public function __construct(int $year, int $month)
    {
        $this->year = $year;
        $this->month = $month;
    }

    public function count(): int
    {
        return count($this->rideList);
    }
}
