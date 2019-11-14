<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Parameter;

use Elastica\Query;

class Order implements ParameterInterface
{
    /** @var string $propertyName */
    protected $propertyName;

    /** @var string $direction */
    protected $direction;

    public function setPropertyName(string $propertyName): Order
    {
        $this->propertyName = $propertyName;

        return $this;
    }

    public function setDirection(string $direction): Order
    {
        $this->direction = $direction;

        return $this;
    }

    public function addToElasticQuery(Query $query): Query
    {
        return $query->addSort([$this->propertyName => ['order' => $this->direction]]);
    }
}
