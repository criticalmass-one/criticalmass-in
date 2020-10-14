<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\City;
use App\Entity\Ride;
use App\Entity\Thread;
use Doctrine\ORM\EntityRepository;

class PostRepository extends EntityRepository
{
    public function findByCrawled(bool $crawled, int $limit = null): array
    {
        $qb = $this->createQueryBuilder('p');

        $qb
            ->where($qb->expr()->eq('p.crawled', ':crawled'))
            ->setParameter('crawled', $crawled);

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        $query = $qb->getQuery();

        return $query->getResult();
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

    public function findPostsForThread(Thread $thread): array
    {
        $builder = $this->createQueryBuilder('p');

        $builder
            ->select('p')
            ->where($builder->expr()->eq('p.thread', ':thread'))
            ->setParameter('thread', $thread)
            ->andWhere($builder->expr()->eq('p.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->addOrderBy('p.dateTime', 'ASC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findForTimelineThreadPostCollector(
        \DateTime $startDateTime = null,
        \DateTime $endDateTime = null,
        $limit = null
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
        \DateTime $startDateTime = null,
        \DateTime $endDateTime = null,
        $limit = null
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
        \DateTime $startDateTime = null,
        \DateTime $endDateTime = null,
        $limit = null
    ): array {
        $builder = $this->createQueryBuilder('p');

        $builder
            ->select('p')
            ->where($builder->expr()->eq('p.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->andWhere($builder->expr()->isNotNull('p.photo'));

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

    public function findForTimelineBlogPostCommentCollector(\DateTime $startDateTime = null, \DateTime $endDateTime = null, $limit = null): array
    {
        $builder = $this->createQueryBuilder('p');

        $builder
            ->select('p', 'bp')
            ->where($builder->expr()->eq('p.enabled', ':enabled'))
            ->join('p.blogPost', 'bp')
            ->andWhere($builder->expr()->eq('bp.enabled', ':enabled'))
            ->addOrderBy('p.dateTime', 'DESC')
            ->setParameter('enabled', true);

        if ($startDateTime) {
            $builder
                ->andWhere($builder->expr()->gte('p.dateTime',':startDateTime'))
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

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }
}
