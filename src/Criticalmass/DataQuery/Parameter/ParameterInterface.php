<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Parameter;

use Elastica\Query;

interface ParameterInterface
{
    public function setEntityFqcn(string $entityFqcn): ParameterInterface;

    public function getEntityFqcn(): string;
    
    public function addToElasticQuery(Query $query): Query;
}