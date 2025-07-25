<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use App\Entity\City;
use App\Entity\Ride;
use Doctrine\ORM\QueryBuilder;
use Elastica\Query\Term;
use MalteHuebner\DataQueryBundle\Attribute\QueryAttribute as DataQuery;
use MalteHuebner\DataQueryBundle\Query\AbstractQuery;
use MalteHuebner\DataQueryBundle\Query\ElasticQueryInterface;
use MalteHuebner\DataQueryBundle\Query\OrmQueryInterface;
use Symfony\Component\Validator\Constraints as Constraints;

#[DataQuery\RequiredEntityProperty(propertyName: 'name')]
class CityNameQuery extends AbstractQuery implements OrmQueryInterface, ElasticQueryInterface
{
    #[Constraints\NotNull]
    protected string $name;

    #[DataQuery\RequiredQueryParameter(
        parameterName: 'name',
        repository: City::class,
        repositoryMethod: 'findOneByName'
    )]
    public function setName(string $name): CityNameQuery
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function createElasticQuery(): \Elastica\Query\AbstractQuery
    {
        return new Term(['city' => $this->name]);
    }

    public function createOrmQuery(QueryBuilder $queryBuilder): QueryBuilder
    {
        $alias = $queryBuilder->getRootAliases()[0];

        if (Ride::class === $this->entityFqcn) {
            $queryBuilder
                ->join(sprintf('%s.city', $alias), 'c')
                ->andWhere($queryBuilder->expr()->eq('c.city', ':city'))
            ;
        }

        if (City::class === $this->entityFqcn) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq(sprintf('%s.city', $alias), ':city'));
        }

        $queryBuilder->setParameter('city', $this->name);

        return $queryBuilder;
    }
}
