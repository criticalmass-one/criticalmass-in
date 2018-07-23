<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Ride;
use App\Entity\RideEstimate;
use Doctrine\ORM\EntityRepository;

class RideEstimateRepository extends EntityRepository
{
    public function findForTimelineRideParticipationEstimateCollector(
        \DateTime $startDateTime = null,
        \DateTime $endDateTime = null,
        $limit = null
    ): array {
        $builder = $this->createQueryBuilder('e');

        $builder
            ->select('e')
            ->where($builder->expr()->isNull('e.track'))
            ->andWhere($builder->expr()->isNull('e.estimatedDistance'))
            ->andWhere($builder->expr()->isNull('e.estimatedDuration'))
            ->andWhere($builder->expr()->isNotNull('e.user'))
            ->addOrderBy('e.dateTime', 'DESC');

        if ($startDateTime) {
            $builder
                ->andWhere($builder->expr()->gte('e.dateTime', ':startDateTime'))
                ->setParameter('startDateTime', $startDateTime);

        }

        if ($endDateTime) {
            $builder
                ->andWhere($builder->expr()->lte('e.dateTime', ':endDateTime'))
                ->setParameter('endDateTime', $endDateTime);
        }

        if ($limit) {
            $builder->setMaxResults($limit);
        }


        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }

    public function findByRideAndParticipants(Ride $ride, int $estimatedParticipants): ?RideEstimate
    {
        $qb = $this->createQueryBuilder('e');

        $qb->where($qb->expr()->eq('e.ride', ':ride'))
            ->andWhere($qb->expr()->eq('e.estimatedParticipants', ':estimatedParticipants'))
            ->setParameter('estimatedParticipants', $estimatedParticipants)
            ->setParameter('ride', $ride)
            ->setMaxResults(1);

        $query = $qb->getQuery();

        return $query->getOneOrNullResult();
    }
}
