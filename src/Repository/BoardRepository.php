<?php declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class BoardRepository extends EntityRepository
{
    public function findEnabledBoards(): array
    {
        $builder = $this->createQueryBuilder('b');

        $builder
            ->select('b')
            ->where($builder->expr()->eq('b.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->orderBy('b.position', 'ASC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findBoardBySlug($slug)
    {
        $builder = $this->createQueryBuilder('board');

        $builder->select('board');
        $builder->where($builder->expr()->eq('board.enabled', 1));
        $builder->andWhere($builder->expr()->eq('board.slug', '\'' . $slug . '\''));

        $query = $builder->getQuery();

        return $query->getSingleResult();
    }
}
