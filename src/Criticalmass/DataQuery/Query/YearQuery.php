<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

class YearQuery implements ElasticQueryInterface, DoctrineQueryInterface
{
    /** @var int $year */
    protected $year;

    public function __construct(int $year)
    {
        $this->year = $year;
    }

    public function getYear(): int
    {
        return $this->year;
    }
}
