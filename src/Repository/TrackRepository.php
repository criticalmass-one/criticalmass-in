<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Ride;
use App\Entity\Track;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class TrackRepository extends EntityRepository
{
    public function getPreviousTrack(Track $track): ?Track
    {
        $builder = $this->createQueryBuilder('t');

        $builder
            ->select('t')
            ->join('t.ride', 'ride')
            ->where($builder->expr()->lt('ride.dateTime', ':dateTime'))
            ->setParameter('dateTime', $track->getRide()->getDateTime())
            ->andWhere($builder->expr()->eq('t.user', ':user'))
            ->setParameter('user', $track->getUser())
            ->addOrderBy('t.startDateTime', 'DESC')
            ->setMaxResults(1);

        $query = $builder->getQuery();

        $result = $query->getOneOrNullResult();

        return $result;
    }

    public function getNextTrack(Track $track): ?Track
    {
        $builder = $this->createQueryBuilder('t');

        $builder
            ->select('t')
            ->join('t.ride', 'ride')
            ->where($builder->expr()->gt('ride.dateTime', ':dateTime'))
            ->setParameter('dateTime', $track->getRide()->getDateTime())
            ->andWhere($builder->expr()->eq('t.user', ':user'))
            ->setParameter('user', $track->getUser())
            ->addOrderBy('t.startDateTime', 'ASC')
            ->setMaxResults(1);

        $query = $builder->getQuery();

        $result = $query->getOneOrNullResult();

        return $result;
    }

    public function findTracksByRide(Ride $ride): array
    {
        $builder = $this->createQueryBuilder('t');

        $builder
            ->select('t')
            ->where($builder->expr()->eq('t.ride', ':ride'))
            ->setParameter('ride', $ride)
            ->andWhere($builder->expr()->eq('t.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->andWhere($builder->expr()->eq('t.deleted', ':deleted'))
            ->setParameter('deleted', false)
            ->addOrderBy('t.startDateTime', 'ASC');

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }

    public function findByUserAndRide(Ride $ride, User $user): ?Track
    {
        $builder = $this->createQueryBuilder('t');

        $builder->select('t')
            ->where($builder->expr()->eq('t.ride', ':ride'))
            ->setParameter('ride', $ride)
            ->andWhere($builder->expr()->eq('t.user', ':user'))
            ->setParameter('user', $user)
            ->andWhere($builder->expr()->eq('t.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->andWhere($builder->expr()->eq('t.deleted', ':deleted'))
            ->setParameter('deleted', false);

        $query = $builder->getQuery();

        $result = $query->getOneOrNullResult();

        return $result;
    }

    public function findForTimelineRideTrackCollector(\DateTime $startDateTime = null, \DateTime $endDateTime = null, $limit = null): array
    {
        $builder = $this->createQueryBuilder('t');

        $builder
            ->select('t')
            ->where($builder->expr()->isNotNull('t.ride'))
            ->andWhere($builder->expr()->isNotNull('t.user'))
            ->andWhere($builder->expr()->eq('t.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->andWhere($builder->expr()->eq('t.deleted', ':deleted'))
            ->setParameter('deleted', false);

        if ($startDateTime) {
            $builder
                ->andWhere($builder->expr()->gte('t.creationDateTime', ':startDateTime'))
                ->setParameter('startDateTime', $startDateTime);
        }

        if ($endDateTime) {
            $builder
                ->andWhere($builder->expr()->lte('t.creationDateTime', ':endDateTime'))
                ->setParameter('endDateTime', $endDateTime);
        }

        if ($limit) {
            $builder->setMaxResults($limit);
        }

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }

    public function countByUser(User $user): int
    {
        $builder = $this->createQueryBuilder('t');

        $builder
            ->select('COUNT(t)')
            ->andWhere($builder->expr()->eq('t.user', ':user'))
            ->andWhere($builder->expr()->eq('t.enabled', ':enabled'))
            ->andWhere($builder->expr()->eq('t.deleted', ':deleted'))
            ->setParameter('user', $user)
            ->setParameter('enabled', true)
            ->setParameter('deleted', false);

        $query = $builder->getQuery();

        return (int) $query->getSingleScalarResult();
    }


    public function findByUser(User $user, bool $enabled = true, bool $deleted = false, string $order = 'DESC'): array
    {
        $query = $this->findByUserQuery($user, $enabled, $deleted, $order);

        return $query->getResult();
    }

    public function findByUserQuery(User $user, bool $enabled = null, bool $deleted = null, string $order = 'DESC'): Query
    {
        $builder = $this->createQueryBuilder('t');

        $builder
            ->join('t.ride', 'r')
            ->where($builder->expr()->eq('t.user', ':user'))
            ->setParameter('user', $user)
            ->orderBy('r.dateTime', $order);

        if ($enabled !== null) {
            $builder
                ->andWhere($builder->expr()->eq('t.enabled', ':enabled'))
                ->setParameter('enabled', $enabled);
        }

        if ($deleted !== null) {
            $builder
                ->andWhere($builder->expr()->eq('t.deleted', ':deleted'))
                ->setParameter('deleted', $deleted);
        }

        return $builder->getQuery();
    }
}

