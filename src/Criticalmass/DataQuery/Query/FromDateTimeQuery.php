<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use MalteHuebner\DataQueryBundle\Annotation\QueryAnnotation as DataQuery;
use MalteHuebner\DataQueryBundle\Query\AbstractDateTimeQuery;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * @DataQuery\RequiredEntityProperty(propertyName="dateTime", propertyType="DateTime")
 */
class FromDateTimeQuery extends AbstractDateTimeQuery
{
    /**
     * @var int $fromDateTime
     */
    #[Constraints\NotNull]
    #[Constraints\Type('int')]
    protected $fromDateTime;

    /**
     * @DataQuery\RequiredQueryParameter(parameterName="fromDateTime")
     */
    public function setFromDateTime(int $fromDateTime): FromDateTimeQuery
    {
        $this->fromDateTime = $fromDateTime;

        return $this;
    }

    public function createElasticQuery(): \Elastica\Query\AbstractQuery
    {
        $dateTimeQuery = new \Elastica\Query\Range($this->propertyName, [
            'gte' => $this->getDateTime()->format($this->dateTimePattern),
            'format' => $this->dateTimeFormat,
        ]);

        return $dateTimeQuery;
    }

    protected function getDateTime(): \DateTime
    {
        return new \DateTime(sprintf('@%d', $this->fromDateTime));
    }

    public function isOverridenBy(): array
    {
        return [];
    }
}
