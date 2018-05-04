<?php declare(strict_types=1);

namespace Criticalmass\Component\Profile\ParticipationTable;

class ParticipationMonth
{
    protected $year;
    protected $month;

    protected $rideList;

    public function __construct(int $year, int $month)
    {
        $this->year = $year;
        $this->month = $month;
    }
}
