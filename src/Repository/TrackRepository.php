<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\City;
use App\Entity\Heatmap;
use App\Entity\Ride;
use App\Entity\Track;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use function Doctrine\ORM\QueryBuilder;

class TrackRepository extends EntityRepository
{
    /** @deprecated */
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
            ->setParameter('deleted', false)
            ->andWhere($builder->expr()->isNotNull('t.polyline'))
            ->andWhere($builder->expr()->isNotNull('t.reducedPolyline'));

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

        return (int)$query->getSingleScalarResult();
    }

    public function findByRide(Ride $ride): array
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
            ->andWhere($builder->expr()->isNotNull('t.polyline'))
            ->andWhere($builder->expr()->isNotNull('t.reducedPolyline'))
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

    public function countByCity(City $city): int
    {
        $builder = $this->createQueryBuilder('t');

        $builder->select('COUNT(t)')
            ->join('t.ride', 'r')
            ->where($builder->expr()->eq('r.city', ':city'))
            ->setParameter('city', $city)
            ->andWhere($builder->expr()->eq('t.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->andWhere($builder->expr()->eq('t.deleted', ':deleted'))
            ->setParameter('deleted', false);

        return (int)$builder->getQuery()->getSingleScalarResult();
    }

    public function findByCity(City $city, string $order = 'ASC'): array
    {
        $query = $this->findByCityQuery($city, $order);

        return $query->getResult();
    }

    public function findByCityQuery(City $city, string $order = 'ASC'): Query
    {
        $builder = $this->createQueryBuilder('t');

        $builder->select('t')
            ->join('t.ride', 'r')
            ->where($builder->expr()->eq('r.city', ':city'))
            ->setParameter('city', $city)
            ->andWhere($builder->expr()->eq('t.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->andWhere($builder->expr()->eq('t.deleted', ':deleted'))
            ->setParameter('deleted', false)
            ->orderBy('r.dateTime', $order);

        return $builder->getQuery();
    }

    public function findUnpaintedTracksForHeatmap(Heatmap $heatmap, int $maxResults = 5, $reviewedOnly = true): array
    {
        $sqb = $this->createQueryBuilder('st');
        $sqb
            ->select('st.id')
            ->join('st.heatmapTracks', 'ht')
            ->where($sqb->expr()->eq('ht.heatmap', ':heatmap'))
            ->setParameter('heatmap', $heatmap);

        $subQuery = $sqb->getQuery();

        $qb = $this->createQueryBuilder('t');

        if ($heatmap->getRide()) {
            $qb->join('t.ride', 'r')
                ->join('r.heatmap', 'h');
        }

        if ($heatmap->getCity()) {
            $qb->join('t.ride', 'r')
                ->join('r.city', 'c')
                ->join('c.heatmap', 'h');
        }

        $qb
            ->andWhere($qb->expr()->eq('h', ':heatmap'))
            ->andWhere($qb->expr()->notIn('t.id', $subQuery->getDQL()))
            ->andWhere($qb->expr()->eq('t.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->andWhere($qb->expr()->eq('t.deleted', ':deleted'))
            ->setParameter('deleted', false)
            ->orderBy('r.dateTime')
            ->setParameter('heatmap', $heatmap);

        if ($maxResults) {
            $qb->setMaxResults($maxResults);
        }

        if ($reviewedOnly) {
            $qb
                ->andWhere($qb->expr()->eq('t.reviewed', ':reviewed'))
                ->setParameter('reviewed', true);
        }

        return $qb->getQuery()->getResult();
    }
}
