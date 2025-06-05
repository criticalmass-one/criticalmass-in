<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use MalteHuebner\DataQueryBundle\Attribute\QueryAttribute as DataQuery;
use MalteHuebner\DataQueryBundle\Query\AbstractDateTimeQuery;
use Symfony\Component\Validator\Constraints as Constraints;

#[DataQuery\RequiredEntityProperty(propertyName: 'dateTime', propertyType: 'DateTime')]
class UntilDateTimeQuery extends AbstractDateTimeQuery
{
    #[Constraints\NotNull]
    #[Constraints\Type('int')]
    protected int $untilDateTime;

    #[DataQuery\RequiredQueryParameter(parameterName: 'untilDateTime')]
    public function setUntilDateTime(int $untilDateTime): UntilDateTimeQuery
    {
        $this->untilDateTime = $untilDateTime;
        return $this;
    }

    public function createElasticQuery(): \Elastica\Query\AbstractQuery
    {
        return new \Elastica\Query\Range($this->propertyName, [
            'lte' => $this->getDateTime()->format($this->dateTimePattern),
            'format' => $this->dateTimeFormat,
        ]);
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
