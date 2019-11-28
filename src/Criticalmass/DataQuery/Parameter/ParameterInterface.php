<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Parameter;

use Elastica\Query;

interface ParameterInterface
{
    public function addToElasticQuery(Query $query): Query;
}