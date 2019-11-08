<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

class DateQuery extends MonthQuery
{
    /** @var int $day */
    protected $day;

    public function __construct(int $year, int $month, int $day)
    {
        $this->day = $day;

        parent::__construct($year, $month);
    }

    public function getDay(): int
    {
        return $this->day;
    }
}
