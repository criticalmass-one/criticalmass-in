<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use App\Criticalmass\DataQuery\Annotation as DataQuery;
use App\Criticalmass\Util\DateTimeUtil;

/**
 * @DataQuery\RequiredEntityProperty(propertyName="simpleDate", propertyType="string")
 */
class MonthQuery extends YearQuery
{
    /** @var int $month */
    protected $month;
    
    /**
     * @DataQuery\RequiredQueryParameter(parameterName="month")
     */
    public function setMonth(int $month): MonthQuery
    {
        $this->month = $month;

        return $this;
    }

    public function createElasticQuery(): \Elastica\Query\AbstractQuery
    {
        $fromDateTime = DateTimeUtil::getMonthStartDateTime($this->toDateTime());
        $untilDateTime = DateTimeUtil::getMonthEndDateTime($this->toDateTime());

        $dateTimeQuery = new \Elastica\Query\Range('simpleDate', [
            'gt' => $fromDateTime->format('Y-m-d'),
            'lte' => $untilDateTime->format('Y-m-d'),
            'format' => 'yyyy-MM-dd',
        ]);

        return $dateTimeQuery;
    }

    protected function toDateTime(): \DateTime
    {
        return new \DateTime(sprintf('%d-%d-01 00:00:00', $this->year, $this->month));
    }

    public function isOverridenBy(): array
    {
        return [DateQuery::class];
    }
}
