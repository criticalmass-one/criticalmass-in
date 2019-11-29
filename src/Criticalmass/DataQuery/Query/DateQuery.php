<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use App\Criticalmass\DataQuery\Annotation\QueryAnnotation as DataQuery;
use App\Criticalmass\Util\DateTimeUtil;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * @DataQuery\RequiredEntityProperty(propertyName="dateTime", propertyType="DateTime")
 */
class DateQuery extends MonthQuery
{
    /**
     * @Constraints\NotNull()
     * @Constraints\Range(min="1", max="31")
     * @Constraints\Type("int")
     * @var int $day
     */
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

        $dateTimeQuery = new \Elastica\Query\Range('exifCreationDate', [
            'gte' => $fromDateTime->format($this->dateTimePattern),
            'lte' => $untilDateTime->format($this->dateTimePattern),
            'format' => $this->dateTimeFormat,
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
