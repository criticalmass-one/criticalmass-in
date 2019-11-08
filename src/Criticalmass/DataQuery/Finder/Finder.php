<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Finder;

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

    public function executeQuery(array $queryList): array
    {
        $useElastic = false;

        /** @var QueryInterface $query */
        foreach ($queryList as $query) {
            if ($query instanceof ElasticQueryInterface) {
                $useElastic = true;

                break;
            }
        }

        $boolQuery = new \Elastica\Query\BoolQuery();

        /** @var ElasticQueryInterface $query */
        foreach ($queryList as $query) {
            $boolQuery->addMust($query->createElasticQuery());
        }

        $query = new \Elastica\Query($boolQuery);

        return $this->elasticFinder->find($query);
    }
}