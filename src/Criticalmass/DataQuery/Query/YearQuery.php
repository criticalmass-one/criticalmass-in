<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use App\Criticalmass\Util\DateTimeUtil;
use Elastica\Query\AbstractQuery;

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

    public function createElasticQuery(): AbstractQuery
    {
        $fromDateTime = DateTimeUtil::getYearStartDateTime($this->toDateTime());
        $untilDateTime = DateTimeUtil::getYearEndDateTime($this->toDateTime());

        $dateTimeQuery = new \Elastica\Query\Range('dateTime', [
            'gt' => $fromDateTime->format('Y-m-d H:i:s'),
            'lte' => $untilDateTime->format('Y-m-d H:i:s'),
            'format' => 'yyyy-MM-dd HH:mm:ss',
        ]);

        return $dateTimeQuery;
    }

    protected function toDateTime(): \DateTime
    {
        return new \DateTime(sprintf('%d-01-01 00:00:00', $this->year));
    }
}
