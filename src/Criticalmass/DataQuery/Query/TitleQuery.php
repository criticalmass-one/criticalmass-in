<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use Doctrine\ORM\AbstractQuery as AbstractOrmQuery;
use Doctrine\ORM\QueryBuilder;
use MalteHuebner\DataQueryBundle\Attribute\QueryAttribute as DataQuery;
use MalteHuebner\DataQueryBundle\Query\AbstractQuery;
use MalteHuebner\DataQueryBundle\Query\OrmQueryInterface;
use MalteHuebner\DataQueryBundle\Query\ElasticQueryInterface;
use Symfony\Component\Validator\Constraints as Constraints;

#[DataQuery\RequiredEntityProperty(propertyName: 'title')]
class TitleQuery extends AbstractQuery implements OrmQueryInterface, ElasticQueryInterface
{
    #[Constraints\NotNull]
    protected string $title;

    #[DataQuery\RequiredQueryParameter(parameterName: 'title')]
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function createElasticQuery(): \Elastica\Query\AbstractQuery
    {
        return new \Elastica\Query\Wildcard('title', sprintf('*%s*', $this->title));
    }

    public function createOrmQuery(QueryBuilder $queryBuilder): AbstractOrmQuery
    {
        $expr = $queryBuilder->expr();
        $alias = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->andWhere($expr->like(sprintf('%s.title', $alias), ':title'))
            ->setParameter('title', sprintf('%%%s%%', $this->title))
        ;

        return $queryBuilder->getQuery();
    }
}
