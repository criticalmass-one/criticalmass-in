<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Parameter;

use Elastica\Query;

class Size implements ParameterInterface
{
    /** @var int $size */
    protected $size;

    public function __construct(int $size)
    {
        $this->size = $size;
    }

    public function addToElasticQuery(Query $query): Query
    {
        return $query->setSize($this->size);
    }
}
