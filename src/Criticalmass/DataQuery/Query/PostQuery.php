<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use App\Entity\Post;
use App\Entity\Ride;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Validator\Constraints as Constraints;

class PostQuery
{
    #[Constraints\NotNull]
    #[Constraints\Type(Ride::class)]
    protected Ride $ride;

    protected ?string $entityFqcn = null;

    public function setRide(Ride $ride): PostQuery
    {
        $this->ride = $ride;

        return $this;
    }

    public function getRide(): Ride
    {
        return $this->ride;
    }

    public function setEntityFqcn(string $entityFqcn): self
    {
        $this->entityFqcn = $entityFqcn;
        return $this;
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
