<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use MalteHuebner\DataQueryBundle\Attribute\QueryAttribute as DataQuery;
use App\Entity\City;
use MalteHuebner\DataQueryBundle\Query\AbstractQuery;
use MalteHuebner\DataQueryBundle\Query\OrmQueryInterface;
use MalteHuebner\DataQueryBundle\Query\ElasticQueryInterface;
use Symfony\Component\Validator\Constraints as Constraints;
use Doctrine\ORM\AbstractQuery as AbstractOrmQuery;
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

    public function createOrmQuery(QueryBuilder $queryBuilder): AbstractOrmQuery
    {
        $alias = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->andWhere($queryBuilder->expr()->eq(sprintf('%s.citySlug', $alias), ':citySlug'))
            ->setParameter('citySlug', $this->city->getMainSlug()->getSlug())
        ;

        return $queryBuilder->getQuery();
    }

    public function isOverridenBy(): array
    {
        return [
            RideQuery::class,
        ];
    }
}
