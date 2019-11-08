<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use App\Criticalmass\Util\DateTimeUtil;
use Elastica\Query\AbstractQuery;

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

    public function createElasticQuery(): AbstractQuery
    {
        $fromDateTime = DateTimeUtil::getMonthStartDateTime($this->toDateTime());
        $untilDateTime = DateTimeUtil::getMonthEndDateTime($this->toDateTime());

        $dateTimeQuery = new \Elastica\Query\Range('dateTime', [
            'gt' => $fromDateTime->format('Y-m-d H:i:s'),
            'lte' => $untilDateTime->format('Y-m-d H:i:s'),
            'format' => 'yyyy-MM-dd HH:mm:ss',
        ]);

        return $dateTimeQuery;
    }

    protected function toDateTime(): \DateTime
    {
        return new \DateTime(sprintf('%d-%d-01 00:00:00', $this->year, $this->month));
    }
}
