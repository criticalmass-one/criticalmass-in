<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use Doctrine\ORM\QueryBuilder;
use App\Entity\Ride;
use Symfony\Component\Validator\Constraints as Constraints;

class RideQuery
{
    #[Constraints\NotNull]
    #[Constraints\Type(Ride::class)]
    protected Ride $ride;

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
