<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use MalteHuebner\DataQueryBundle\Annotation\QueryAnnotation as DataQuery;
use MalteHuebner\DataQueryBundle\Query\AbstractDateTimeQuery;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * @DataQuery\RequiredEntityProperty(propertyName="dateTime", propertyType="DateTime")
 */
class UntilDateTimeQuery extends AbstractDateTimeQuery
{
    /**
     * @var int $untilDateTime
     */
    #[Constraints\NotNull]
    #[Constraints\Type('int')]
    protected $untilDateTime;

    /**
     * @DataQuery\RequiredQueryParameter(parameterName="untilDateTime")
     */
    public function setUntilDateTime(int $untilDateTime): UntilDateTimeQuery
    {
        $this->untilDateTime = $untilDateTime;

        return $this;
    }

    public function createElasticQuery(): \Elastica\Query\AbstractQuery
    {
        $dateTimeQuery = new \Elastica\Query\Range($this->propertyName, [
            'lte' => $this->getDateTime()->format($this->dateTimePattern),
            'format' => $this->dateTimeFormat,
        ]);

        return $dateTimeQuery;
    }

    protected function getDateTime(): \DateTime
    {
        return new \DateTime(sprintf('@%d', $this->untilDateTime));
    }

    public function isOverridenBy(): array
    {
        return [];
    }
}
