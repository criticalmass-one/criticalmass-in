<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Validator\Constraints as Constraints;

class TitleQuery
{
    #[Constraints\NotNull]
    protected string $title;

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

    public function createOrmQuery(QueryBuilder $queryBuilder): QueryBuilder
    {
        $expr = $queryBuilder->expr();
        $alias = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->andWhere($expr->like(sprintf('%s.title', $alias), ':title'))
            ->setParameter('title', sprintf('%%%s%%', $this->title))
        ;

        return $queryBuilder;
    }
}
