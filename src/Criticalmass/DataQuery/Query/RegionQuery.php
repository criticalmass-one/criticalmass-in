<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use App\Entity\Region;
use Doctrine\ORM\QueryBuilder;
use Elastica\Query\BoolQuery;
use Elastica\Query\Term;
use MalteHuebner\DataQueryBundle\Attribute\QueryAttribute as DataQuery;
use MalteHuebner\DataQueryBundle\Query\AbstractQuery;
use MalteHuebner\DataQueryBundle\Query\ElasticQueryInterface;
use MalteHuebner\DataQueryBundle\Query\OrmQueryInterface;
use Symfony\Component\Validator\Constraints as Constraints;

#[DataQuery\RequiredEntityProperty(propertyName: 'region')]
class RegionQuery extends AbstractQuery implements OrmQueryInterface, ElasticQueryInterface
{
    #[Constraints\NotNull]
    #[Constraints\Type(Region::class)]
    protected Region $region;

    #[DataQuery\RequiredQueryParameter(parameterName: 'regionSlug')]
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
