<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use Doctrine\ORM\QueryBuilder;
use MalteHuebner\DataQueryBundle\Attribute\QueryAttribute as DataQuery;
use App\Entity\Ride;
use MalteHuebner\DataQueryBundle\Query\AbstractQuery;
use MalteHuebner\DataQueryBundle\Query\OrmQueryInterface;
use MalteHuebner\DataQueryBundle\Query\ElasticQueryInterface;
use Symfony\Component\Validator\Constraints as Constraints;

#[DataQuery\RequiredEntityProperty(propertyName: 'slug')]
class RideQuery extends AbstractQuery implements OrmQueryInterface, ElasticQueryInterface
{
    #[Constraints\NotNull]
    #[Constraints\Type(Ride::class)]
    protected Ride $ride;

    #[DataQuery\RequiredQueryParameter(parameterName: 'rideIdentifier')]
    public function setRide(Ride $ride): RideQuery
    {
        $this->ride = $ride;
        return $this;
    }

    public function getRide(): Ride
    {
        return $this->ride;
    }

    public function createElasticQuery(): \Elastica\Query\AbstractQuery
    {
        return new \Elastica\Query\Term(['rideId' => $this->getRide()->getId()]);
    }

    public function createOrmQuery(QueryBuilder $queryBuilder): QueryBuilder
    {
        $expr = $queryBuilder->expr();
        $alias = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->andWhere($expr->eq(sprintf('%s.id', $alias), ':rideId'))
            ->setParameter('rideId', $this->ride->getId());

        return $queryBuilder;
    }
}
