<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Parameter;

use Elastica\Query;

class Order implements ParameterInterface
{
    /** @var string $propertyName */
    protected $propertyName;

    /** @var string $direction */
    protected $direction;

    public function __construct(string $propertyName, string $direction)
    {
        $this->propertyName = $propertyName;
        $this->direction = $direction;
    }

    public function addToElasticQuery(Query $query): Query
    {
        return $query->addSort([$this->propertyName => ['order' => $this->direction]]);
    }
}
