<?php declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * @package App\Repository
 * @author maltehuebner
 * @since 2016-02-26
 */
class BoardRepository extends EntityRepository
{
    public function findEnabledBoards()
    {
        $builder = $this->createQueryBuilder('board');

        $builder->select('board');
        $builder->where($builder->expr()->eq('board.enabled', 1));
        $builder->orderBy('board.position', 'ASC');

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
