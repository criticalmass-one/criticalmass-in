<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use Doctrine\ORM\AbstractQuery as AbstractOrmQuery;
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

    #[DataQuery\RequiredQueryParameter(parameterName: 'name')]
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

    public function createOrmQuery(QueryBuilder $queryBuilder): AbstractOrmQuery
    {
        $alias = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->andWhere($queryBuilder->expr()->eq(sprintf('%s.city', $alias), ':city'))
            ->setParameter('city', $this->name);

        return $queryBuilder->getQuery();
    }
}
