<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Parameter;

use Elastica\Query;

class From implements ParameterInterface
{
    /** @var int $from */
    protected $from;

    public function __construct(int $from)
    {
        $this->from = $from;
    }

    public function addToElasticQuery(Query $query): Query
    {
        return $query->setFrom($this->from);
    }
}
