<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\City;
use App\Entity\Post;
use App\Entity\Ride;
use App\Entity\Thread;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function getPostsForRide(Ride $ride): array
    {
        $builder = $this->createQueryBuilder('p');

        $builder
            ->select('p')
            ->where($builder->expr()->eq('p.ride', ':ride'))
            ->setParameter('ride', $ride)
            ->andWhere($builder->expr()->eq('p.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->addOrderBy('p.dateTime', 'ASC');

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }

    public function countPostsForCityRides(City $city): int
    {
        $builder = $this->createQueryBuilder('p');

        $builder
            ->select('COUNT(p.id)')
            ->join('p.ride', 'ride')
            ->where($builder->expr()->eq('ride.city', ':city'))
            ->setParameter('city', $city);

        $query = $builder->getQuery();

        return (int) $query->getSingleScalarResult();
    }

    public function getPostsForCityRides(City $city): array
    {
        $builder = $this->createQueryBuilder('p');

        $builder
            ->select('p')
            ->join('p.ride', 'ride')
            ->where($builder->expr()->eq('ride.city', ':city'))
            ->setParameter('city', $city);

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function countPostsForThread(Thread $thread): int
    {
        $builder = $this->createQueryBuilder('p');

        $builder
            ->select('COUNT(p.id)')
            ->where($builder->expr()->eq('p.thread', ':thread'))
            ->setParameter('thread', $thread)
            ->andWhere($builder->expr()->eq('p.enabled', ':enabled'))
            ->setParameter('enabled', true);

        return (int) $builder->getQuery()->getSingleScalarResult();
    }

    public function findLatestPostForThread(Thread $thread): ?Post
    {
        $builder = $this->createQueryBuilder('p');

        $builder
            ->select('p')
            ->where($builder->expr()->eq('p.thread', ':thread'))
            ->setParameter('thread', $thread)
            ->andWhere($builder->expr()->eq('p.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->orderBy('p.dateTime', 'DESC')
            ->setMaxResults(1);

        return $builder->getQuery()->getOneOrNullResult();
    }

    public function findPostsForThread(Thread $thread): array
    {
        return $this->queryPostsForThread($thread)->getResult();
    }

    /**
     * Die Abfrage statt des Ergebnisses — der Paginator braucht sie, um selbst zu begrenzen.
     */
    public function queryPostsForThread(Thread $thread): Query
    {
        $builder = $this->createQueryBuilder('p');

        $builder
            ->select('p')
            ->where($builder->expr()->eq('p.thread', ':thread'))
            ->setParameter('thread', $thread)
            ->andWhere($builder->expr()->eq('p.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->addOrderBy('p.dateTime', 'ASC');

        return $builder->getQuery();
    }

    /**
     * Der wievielte Beitrag eines Themas ist das? Aus der Position folgt die Seite,
     * auf der ein Dauerlink wie #post-42 tatsaechlich zu finden ist.
     */
    public function findPositionInThread(Post $post): int
    {
        $thread = $post->getThread();

        if (null === $thread) {
            return 1;
        }

        $builder = $this->createQueryBuilder('p');

        $builder
            ->select('COUNT(p.id)')
            ->where($builder->expr()->eq('p.thread', ':thread'))
            ->setParameter('thread', $thread)
            ->andWhere($builder->expr()->eq('p.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->andWhere($builder->expr()->lte('p.dateTime', ':dateTime'))
            ->setParameter('dateTime', $post->getDateTime());

        return max(1, (int) $builder->getQuery()->getSingleScalarResult());
    }

    public function findForTimelineThreadPostCollector(
        ?\DateTime $startDateTime = null,
        ?\DateTime $endDateTime = null,
        ?int $limit = null
    ): array {
        $builder = $this->createQueryBuilder('p');

        $builder
            ->select('p')
            ->join('p.thread', 'thread')
            ->where($builder->expr()->eq('p.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->andWhere($builder->expr()->isNotNull('p.thread'))
            ->andWhere($builder->expr()->neq('p', 'thread.firstPost'));

        if ($startDateTime) {
            $builder
                ->andWhere($builder->expr()->gte('p.dateTime', ':startDateTime'))
                ->setParameter('startDateTime', $startDateTime);
        }

        if ($endDateTime) {
            $builder
                ->andWhere($builder->expr()->lte('p.dateTime', ':endDateTime'))
                ->setParameter('endDateTime', $endDateTime);
        }

        if ($limit) {
            $builder->setMaxResults($limit);
        }

        $builder->addOrderBy('p.dateTime', 'DESC');

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }

    public function findForTimelineRideCommentCollector(
        ?\DateTime $startDateTime = null,
        ?\DateTime $endDateTime = null,
        ?int $limit = null
    ): array {
        $builder = $this->createQueryBuilder('p');

        $builder
            ->select('p')
            ->where($builder->expr()->eq('p.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->andWhere($builder->expr()->isNotNull('p.ride'));

        if ($startDateTime) {
            $builder
                ->andWhere($builder->expr()->gte('p.dateTime', ':startDateTime'))
                ->setParameter('startDateTime', $startDateTime);
        }

        if ($endDateTime) {
            $builder
                ->andWhere($builder->expr()->lte('p.dateTime', ':endDateTime'))
                ->setParameter('endDateTime', $endDateTime);
        }

        if ($limit) {
            $builder->setMaxResults($limit);
        }

        $builder->addOrderBy('p.dateTime', 'DESC');

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }

    public function findForTimelinePhotoCommentCollector(
        ?\DateTime $startDateTime = null,
        ?\DateTime $endDateTime = null,
        ?int $limit = null
    ): array {
        $builder = $this->createQueryBuilder('p');

        $builder
            ->select('p')
            ->join('p.photo', 'p2')
            ->where($builder->expr()->eq('p.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->andWhere($builder->expr()->eq('p2.deleted', ':deleted'))
            ->setParameter('deleted', false);

        if ($startDateTime) {
            $builder
                ->andWhere($builder->expr()->gte('p.dateTime', ':startDateTime'))
                ->setParameter('startDateTime', $startDateTime);
        }

        if ($endDateTime) {
            $builder
                ->andWhere($builder->expr()->lte('p.dateTime', ':endDateTime'))
                ->setParameter('endDateTime', $endDateTime);
        }

        if ($limit) {
            $builder->setMaxResults($limit);
        }

        $builder->addOrderBy('p.dateTime', 'DESC');

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }
}
