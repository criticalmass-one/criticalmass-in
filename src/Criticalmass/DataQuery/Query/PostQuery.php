<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use App\Entity\Post;
use App\Entity\Ride;
use Doctrine\ORM\QueryBuilder;
use MalteHuebner\DataQueryBundle\Attribute\QueryAttribute as DataQuery;
use MalteHuebner\DataQueryBundle\Query\AbstractQuery;
use MalteHuebner\DataQueryBundle\Query\OrmQueryInterface;
use Symfony\Component\Validator\Constraints as Constraints;

#[DataQuery\RequiredEntityProperty(propertyName: 'ride')]
class PostQuery extends AbstractQuery implements OrmQueryInterface
{
    #[Constraints\NotNull]
    #[Constraints\Type(Ride::class)]
    protected Ride $ride;

    #[DataQuery\RequiredQueryParameter(parameterName: 'rideIdentifier')]
    public function setRide(Ride $ride): PostQuery
    {
        $this->ride = $ride;

        return $this;
    }

    public function getRide(): Ride
    {
        return $this->ride;
    }

    public function createOrmQuery(QueryBuilder $queryBuilder): QueryBuilder
    {
        $alias = $queryBuilder->getRootAliases()[0];

        if (Post::class === $this->entityFqcn) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->eq(sprintf('%s.ride', $alias), ':ride'))
                ->setParameter('ride', $this->ride)
            ;
        }

        return $queryBuilder;
    }
}
