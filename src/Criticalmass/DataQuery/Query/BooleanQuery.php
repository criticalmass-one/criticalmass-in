<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

class BooleanQuery extends AbstractQuery implements DoctrineQueryInterface, ElasticQueryInterface
{
    /** @var string $propertyName */
    protected $propertyName;

    /** @var bool $value */
    protected $value = false;

    public function setPropertyName(string $propertyName): BooleanQuery
    {
        $this->propertyName = $propertyName;

        return $this;
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    public function setValue(bool $value): BooleanQuery
    {
        $this->value = $value;

        return $this;
    }

    public function getValue(): bool
    {
        return $this->value;
    }

    public function createElasticQuery(): \Elastica\Query\AbstractQuery
    {
        return new \Elastica\Query\Term([$this->propertyName => $this->value]);
    }
}
