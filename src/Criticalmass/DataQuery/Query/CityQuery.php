<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use App\Entity\Ride;
use MalteHuebner\DataQueryBundle\Attribute\QueryAttribute as DataQuery;
use App\Entity\City;
use MalteHuebner\DataQueryBundle\Query\AbstractQuery;
use MalteHuebner\DataQueryBundle\Query\OrmQueryInterface;
use MalteHuebner\DataQueryBundle\Query\ElasticQueryInterface;
use Symfony\Component\Validator\Constraints as Constraints;
use Doctrine\ORM\QueryBuilder;

#[DataQuery\RequiredEntityProperty(propertyName: 'slug')]
class CityQuery extends AbstractQuery implements OrmQueryInterface, ElasticQueryInterface
{
    #[Constraints\NotNull]
    #[Constraints\Type(City::class)]
    protected City $city;

    #[DataQuery\RequiredQueryParameter(parameterName: 'citySlug')]
    public function setCity(City $city): CityQuery
    {
        $this->city = $city;

        return $this;
    }

    public function getCity(): City
    {
        return $this->city;
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
