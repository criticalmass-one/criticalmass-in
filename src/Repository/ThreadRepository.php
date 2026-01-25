<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Board;
use App\Entity\City;
use App\Entity\Thread;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ThreadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Thread::class);
    }

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

    public function findThreadBySlug(string $slug): ?Thread
    {
        $builder = $this->createQueryBuilder('t');

        $builder
            ->select('t')
            ->where($builder->expr()->eq('t.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->andWhere($builder->expr()->eq('t.slug', ':slug'))
            ->setParameter('slug', $slug);

        $query = $builder->getQuery();

        return $query->getSingleResult();
    }

    public function findForTimelineThreadCollector(?\DateTime $startDateTime = null, ?\DateTime $endDateTime = null, $limit = null): array
    {
        $builder = $this->createQueryBuilder('t');

        $builder
            ->select('t')
            ->join('t.firstPost', 'firstPost')
            ->where($builder->expr()->eq('t.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->addOrderBy('firstPost.dateTime', 'DESC');

        if ($startDateTime) {
            $builder
                ->andWhere($builder->expr()->gte('firstPost.dateTime',':startDateTime'))
                ->setParameter('startDateTime', $startDateTime);
        }

        if ($endDateTime) {
            $builder
                ->andWhere($builder->expr()->lte('firstPost.dateTime', ':endDateTime'))
                ->setParameter('endDateTime', $endDateTime);
        }

        if ($limit) {
            $builder->setMaxResults($limit);
        }

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }
}

