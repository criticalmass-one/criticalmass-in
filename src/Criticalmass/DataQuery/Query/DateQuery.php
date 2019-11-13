<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use App\Criticalmass\DataQuery\Annotation as DataQuery;
use App\Criticalmass\Util\DateTimeUtil;

/**
 * @DataQuery\RequiredEntityProperty(propertyName="simpleDate", propertyType="string")
 */
class DateQuery extends MonthQuery
{
    /** @var int $day */
    protected $day;

    /**
     * @DataQuery\RequiredQueryParameter(parameterName="day")
     */
    public function setDay(int $day): DateQuery
    {
        $this->day = $day;

        return $this;
    }

    public function createElasticQuery(): \Elastica\Query\AbstractQuery
    {
        $fromDateTime = DateTimeUtil::getDayStartDateTime($this->toDateTime());
        $untilDateTime = DateTimeUtil::getDayEndDateTime($this->toDateTime());

        $dateTimeQuery = new \Elastica\Query\Range('simpleDate', [
            'gte' => $fromDateTime->format('Y-m-d'),
            'lte' => $untilDateTime->format('Y-m-d'),
            'format' => 'yyyy-MM-dd',
        ]);

        return $dateTimeQuery;
    }

    protected function toDateTime(): \DateTime
    {
        return new \DateTime(sprintf('%d-%d-%d 00:00:00', $this->year, $this->month, $this->day));
    }

    public function isOverridenBy(): array
    {
        return [];
    }
}
