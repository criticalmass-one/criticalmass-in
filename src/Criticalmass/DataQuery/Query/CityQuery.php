<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use App\Entity\CitySlug;
use App\Entity\Photo;
use App\Entity\Post;
use App\Entity\Ride;
use App\Entity\Track;
use App\Entity\City;
use Symfony\Component\Validator\Constraints as Constraints;
use Doctrine\ORM\QueryBuilder;

class CityQuery
{
    #[Constraints\NotNull]
    #[Constraints\Type(City::class)]
    protected City $city;

    protected ?string $entityFqcn = null;

    public function setCity(City $city): CityQuery
    {
        $this->city = $city;

        return $this;
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function setEntityFqcn(string $entityFqcn): self
    {
        $this->entityFqcn = $entityFqcn;
        return $this;
    }

    public function createElasticQuery(): \Elastica\Query\AbstractQuery
    {
        return new \Elastica\Query\Term(['city' => $this->city->getCity()]);
    }

    public function createOrmQuery(QueryBuilder $queryBuilder): QueryBuilder
    {
        $alias = $queryBuilder->getRootAliases()[0];

        if (Ride::class === $this->entityFqcn) {
            $queryBuilder
                ->join(sprintf('%s.city', $alias), 'c')
                ->join('c.mainSlug', 'cs')
            ;
        }

        if (City::class === $this->entityFqcn) {
            $queryBuilder->join(sprintf('%s.mainSlug', $alias), 'cs');
        }

        // Photo has a direct city relation
        if (Photo::class === $this->entityFqcn) {
            $queryBuilder
                ->join(sprintf('%s.city', $alias), 'c')
                ->join('c.mainSlug', 'cs')
            ;
        }

        // Post has a direct city relation
        if (Post::class === $this->entityFqcn) {
            $queryBuilder
                ->join(sprintf('%s.city', $alias), 'c')
                ->join('c.mainSlug', 'cs')
            ;
        }

        // Track has city through ride relation
        if (Track::class === $this->entityFqcn) {
            $queryBuilder
                ->join(sprintf('%s.ride', $alias), 'r')
                ->join('r.city', 'c')
                ->join('c.mainSlug', 'cs')
            ;
        }

        $queryBuilder
            ->andWhere($queryBuilder->expr()->eq('cs.slug', ':citySlug'))
            ->setParameter('citySlug', $this->city->getMainSlug()->getSlug())
        ;

        return $queryBuilder;
    }

    public function isOverridenBy(): array
    {
        return [
            RideQuery::class,
        ];
    }
}
