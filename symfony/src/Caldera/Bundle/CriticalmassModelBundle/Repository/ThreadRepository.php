<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Repository;

use Caldera\Bundle\CriticalmassModelBundle\Entity\Board;
use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Doctrine\ORM\EntityRepository;

/**
 * @package Caldera\Bundle\CriticalmassModelBundle\Repository
 * @author maltehuebner
 * @since 2016-02-13
 */
class ThreadRepository extends EntityRepository
{
    public function findThreadsForBoard(Board $board)
    {
        $builder = $this->createQueryBuilder('thread');

        $builder->select('thread');

        $builder->leftJoin('thread.lastPost', 'lastPost');

        $builder->where($builder->expr()->eq('thread.board', $board->getId()));
        $builder->andWhere($builder->expr()->eq('thread.enabled', 1));

        $builder->orderBy('lastPost.dateTime', 'DESC');
        
        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findThreadsForCity(City $city)
    {
        $builder = $this->createQueryBuilder('thread');

        $builder->select('thread');

        $builder->where($builder->expr()->eq('thread.city', $city->getId()));
        $builder->andWhere($builder->expr()->eq('thread.enabled', 1));

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findThreadBySlug($slug)
    {
        $builder = $this->createQueryBuilder('thread');

        $builder->select('thread');
        $builder->where($builder->expr()->eq('thread.enabled', 1));
        $builder->andWhere($builder->expr()->eq('thread.slug', '\''.$slug.'\''));

        $query = $builder->getQuery();

        return $query->getSingleResult();
    }
}

