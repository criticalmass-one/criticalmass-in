<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Board;
use App\Entity\City;
use Doctrine\ORM\EntityRepository;

class ThreadRepository extends EntityRepository
{
    public function findThreadsForBoard(Board $board): array
    {
        $builder = $this->createQueryBuilder('t');

        $builder
            ->select('t')
            ->leftJoin('t.lastPost', 'lastPost')
            ->where($builder->expr()->eq('t.board', ':board'))
            ->setParameter('board', $board)
            ->andWhere($builder->expr()->eq('t.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->orderBy('lastPost.dateTime', 'DESC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findThreadsForCity(City $city): array
    {
        $builder = $this->createQueryBuilder('t');

        $builder
            ->select('t')
            ->leftJoin('t.lastPost', 'lastPost')
            ->where($builder->expr()->eq('t.city', ':city'))
            ->setParameter('city', $city)
            ->andWhere($builder->expr()->eq('t.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->orderBy('lastPost.dateTime', 'DESC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findThreadBySlug($slug)
    {
        $builder = $this->createQueryBuilder('thread');

        $builder->select('thread');
        $builder->where($builder->expr()->eq('thread.enabled', 1));
        $builder->andWhere($builder->expr()->eq('thread.slug', '\'' . $slug . '\''));

        $query = $builder->getQuery();

        return $query->getSingleResult();
    }

    public function findForTimelineThreadCollector(
        \DateTime $startDateTime = null,
        \DateTime $endDateTime = null,
        $limit = null
    ) {
        $builder = $this->createQueryBuilder('thread');

        $builder->select('thread');

        $builder->join('thread.firstPost', 'firstPost');

        $builder->where($builder->expr()->eq('thread.enabled', 1));

        if ($startDateTime) {
            $builder->andWhere($builder->expr()->gte('firstPost.dateTime',
                '\'' . $startDateTime->format('Y-m-d H:i:s') . '\''));
        }

        if ($endDateTime) {
            $builder->andWhere($builder->expr()->lte('firstPost.dateTime',
                '\'' . $endDateTime->format('Y-m-d H:i:s') . '\''));
        }

        if ($limit) {
            $builder->setMaxResults($limit);
        }

        $builder->addOrderBy('firstPost.dateTime', 'DESC');

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }
}

