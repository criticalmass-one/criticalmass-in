<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Finder;

use App\Criticalmass\DataQuery\Parameter\ParameterInterface;
use App\Criticalmass\DataQuery\Query\ElasticQueryInterface;
use App\Criticalmass\DataQuery\Query\QueryInterface;
use FOS\ElasticaBundle\Repository;

class Finder implements FinderInterface
{
    protected Repository $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function executeQuery(array $queryList, array $parameterList): array
    {
        return $this->executeElasticQuery($queryList, $parameterList);
    }

    protected function executeElasticQuery(array $queryList, array $parameterList): array
    {
        $boolQuery = new \Elastica\Query\BoolQuery();

        /** @var ElasticQueryInterface $query */
        foreach ($queryList as $query) {
            if ($query instanceof QueryInterface) {
                $boolQuery->addMust($query->createElasticQuery());
            }
        }

        $query = new \Elastica\Query($boolQuery);

        /** @var ParameterInterface $parameter */
        foreach ($parameterList as $parameter) {
            if ($parameter instanceof ParameterInterface) {
                $query = $parameter->addToElasticQuery($query);
            }
        }

        //dump(json_encode($query->toArray()));

        return $this->repository->find($query);
    }

    protected function executeOrmQuery(array $queryList): array
    {
        return [];
    }
}