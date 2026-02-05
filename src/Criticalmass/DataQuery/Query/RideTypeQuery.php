<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Validator\Constraints as Constraints;

class RideTypeQuery
{
    #[Constraints\NotNull]
    protected string $rideType;

    public function setRideType(string $rideType): self
    {
        $this->rideType = $rideType;
        return $this;
    }

    public function getRideType(): string
    {
        return $this->rideType;
    }

    public function createElasticQuery(): \Elastica\Query\AbstractQuery
    {
        return new \Elastica\Query\Term(['rideType' => strtoupper($this->rideType)]);
    }

    public function createOrmQuery(QueryBuilder $queryBuilder): QueryBuilder
    {
        $expr = $queryBuilder->expr();
        $alias = $queryBuilder->getRootAliases()[0];

        $rideTypes = array_map('strtoupper', explode(',', $this->rideType));

        if (count($rideTypes) === 1) {
            $queryBuilder
                ->andWhere($expr->eq(sprintf('%s.rideType', $alias), ':rideType'))
                ->setParameter('rideType', $rideTypes[0]);
        } else {
            $queryBuilder
                ->andWhere($expr->in(sprintf('%s.rideType', $alias), ':rideTypes'))
                ->setParameter('rideTypes', $rideTypes);
        }

        return $queryBuilder;
    }
}
