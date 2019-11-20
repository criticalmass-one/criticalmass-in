<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use App\Criticalmass\DataQuery\Annotation as DataQuery;
use App\Criticalmass\Util\DateTimeUtil;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * @DataQuery\RequiredEntityProperty(propertyName="dateTime", propertyType="DateTime")
 */
class YearQuery extends AbstractQuery implements ElasticQueryInterface, DoctrineQueryInterface
{
    /**
     * @Constraints\NotNull()
     * @Constraints\GreaterThanOrEqual(value="1990")
     * @Constraints\Type("int")
     * @var int $year
     */
    protected $year;

    /**
     * @DataQuery\RequiredQueryParameter(parameterName="year")
     */
    public function setYear(int $year): YearQuery
    {
        $this->year = $year;

        return $this;
    }

    public function createElasticQuery(): \Elastica\Query\AbstractQuery
    {
        $fromDateTime = DateTimeUtil::getYearStartDateTime($this->toDateTime());
        $untilDateTime = DateTimeUtil::getYearEndDateTime($this->toDateTime());

        $dateTimeQuery = new \Elastica\Query\Range('dateTime', [
            'gte' => $fromDateTime->format('Y-m-d'),
            'lte' => $untilDateTime->format('Y-m-d'),
            'format' => 'strict_date_optional_time',
        ]);

        return $dateTimeQuery;
    }

    protected function toDateTime(): \DateTime
    {
        return new \DateTime(sprintf('%d-01-01 00:00:00', $this->year));
    }

    public function isOverridenBy(): array
    {
        return [
            MonthQuery::class,
            DateQuery::class,
        ];
    }
}