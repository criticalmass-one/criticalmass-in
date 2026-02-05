<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use App\Entity\Region;
use Doctrine\ORM\QueryBuilder;
use Elastica\Query\BoolQuery;
use Elastica\Query\Term;
use Symfony\Component\Validator\Constraints as Constraints;

class RegionQuery
{
    #[Constraints\NotNull]
    #[Constraints\Type(Region::class)]
    protected Region $region;

    public function setRegion(Region $region): RegionQuery
    {
        $this->region = $region;
        return $this;
    }

    public function getRegion(): Region
    {
        return $this->region;
    }

    public function createElasticQuery(): \Elastica\Query\AbstractQuery
    {
        $regionName = $this->region->getName();

        $regionQuery = new BoolQuery();
        $regionQuery->addShould(new Term(['region' => $regionName]));
        $regionQuery->addShould(new Term(['country' => $regionName]));
        $regionQuery->addShould(new Term(['continent' => $regionName]));

        return $regionQuery;
    }

    public function createOrmQuery(QueryBuilder $queryBuilder): QueryBuilder
    {
        $expr = $queryBuilder->expr();

        $queryBuilder
            ->andWhere($expr->eq('e.region', ':region'))
            ->setParameter('region', $this->region);

        return $queryBuilder;
    }
}
