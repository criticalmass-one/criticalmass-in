<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Finder;

use App\Criticalmass\DataQuery\Parameter\ParameterInterface;
use App\Criticalmass\DataQuery\Query\ElasticQueryInterface;
use App\Criticalmass\DataQuery\Query\QueryInterface;
use FOS\ElasticaBundle\Finder\FinderInterface as FOSFinderInterface;

class Finder implements FinderInterface
{
    /** @var FOSFinderInterface $elasticFinder */
    protected $elasticFinder;

    public function __construct(FOSFinderInterface $elasticFinder)
    {
        $this->elasticFinder = $elasticFinder;
    }

    public function executeQuery(array $queryList, array $parameterList): array
    {
        $useElastic = false;

        /** @var QueryInterface $query */
        foreach ($queryList as $query) {
            if ($query instanceof ElasticQueryInterface) {
                $useElastic = true;

                break;
            }
        }

        if ($useElastic) {
            return $this->executeElasticQuery($queryList, $parameterList);
        }

        return $this->executeOrmQuery($queryList);
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

        /** @var ElasticQueryInterface $query */
        foreach ($parameterList as $parameter) {
            if ($parameter instanceof ParameterInterface) {
                $parameter->addToElasticQuery($query);
            }
        }

        dump($query->toArray());
        return $this->elasticFinder->find($query);
    }

    protected function executeOrmQuery(array $queryList): array
    {
        return [];
    }
}