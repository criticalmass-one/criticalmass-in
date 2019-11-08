<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

class MonthQuery extends YearQuery
{
    /** @var int $month */
    protected $month;

    public function __construct(int $year, int $month)
    {
        $this->month = $month;

        parent::__construct($year);
    }

    public function getMonth(): int
    {
        return $this->month;
    }
}
