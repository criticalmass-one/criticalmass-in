<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use App\Criticalmass\DataQuery\Annotation as DataQuery;
use App\Criticalmass\Util\DateTimeUtil;
use Elastica\Query\AbstractQuery;

/**
 * @DataQuery\RequiredEntityProperty(propertyName="simpleDate", propertyType="string")
 */
class YearQuery implements ElasticQueryInterface, DoctrineQueryInterface
{
    /** @var int $year */
    protected $year;

    /**
     * @DataQuery\RequiredQueryParameter(parameterName="year")
     */
    public function setYear(int $year): YearQuery
    {
        $this->year = $year;

        return $this;
    }

    public function createElasticQuery(): AbstractQuery
    {
        $fromDateTime = DateTimeUtil::getYearStartDateTime($this->toDateTime());
        $untilDateTime = DateTimeUtil::getYearEndDateTime($this->toDateTime());

        $dateTimeQuery = new \Elastica\Query\Range('simpleDate', [
            'gt' => $fromDateTime->format('Y-m-d'),
            'lte' => $untilDateTime->format('Y-m-d'),
            'format' => 'yyyy-MM-dd',
        ]);

        return $dateTimeQuery;
    }

    protected function toDateTime(): \DateTime
    {
        return new \DateTime(sprintf('%d-01-01 00:00:00', $this->year));
    }
}
