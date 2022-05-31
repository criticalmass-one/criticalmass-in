<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use App\Criticalmass\DataQuery\Annotation\QueryAnnotation as DataQuery;
use App\Criticalmass\Util\DateTimeUtil;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * @DataQuery\RequiredEntityProperty(propertyName="dateTime", propertyType="DateTime")
 */
class MonthQuery extends YearQuery
{
    /**
     * @Constraints\NotNull()
     * @Constraints\Range(min="1", max="12")
     * @Constraints\Type("int")
     * @var int $month
     */
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

        $dateTimeQuery = new \Elastica\Query\Range($this->propertyName, [
            'gte' => $fromDateTime->format($this->dateTimePattern),
            'lte' => $untilDateTime->format($this->dateTimePattern),
            'format' => $this->dateTimeFormat,
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
