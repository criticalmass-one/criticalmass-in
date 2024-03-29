<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use MalteHuebner\DataQueryBundle\Annotation\QueryAnnotation as DataQuery;
use App\Criticalmass\Util\DateTimeUtil;
use MalteHuebner\DataQueryBundle\Query\AbstractDateTimeQuery;
use MalteHuebner\DataQueryBundle\Query\DoctrineQueryInterface;
use MalteHuebner\DataQueryBundle\Query\ElasticQueryInterface;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * @DataQuery\RequiredEntityProperty(propertyName="dateTime", propertyType="DateTime")
 */
class YearQuery extends AbstractDateTimeQuery implements ElasticQueryInterface, DoctrineQueryInterface
{
    /**
     * @var int $year
     */
    #[Constraints\NotNull]
    #[Constraints\GreaterThanOrEqual(value: 1990)]
    #[Constraints\Type('int')]
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

        $dateTimeQuery = new \Elastica\Query\Range($this->propertyName, [
            'gte' => $fromDateTime->format($this->dateTimePattern),
            'lte' => $untilDateTime->format($this->dateTimePattern),
            'format' => $this->dateTimeFormat,
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
