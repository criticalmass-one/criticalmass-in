<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class RideEstimateRepository extends EntityRepository
{
    public function findForTimelineRideParticipantsEstimateCollector(\DateTime $startDateTime = null, \DateTime $endDateTime = null, $limit = null)
    {
        $builder = $this->createQueryBuilder('e');

        $builder->select('e');
        $builder->where($builder->expr()->isNull('e.track'));
        $builder->andWhere($builder->expr()->isNull('e.estimatedDistance'));
        $builder->andWhere($builder->expr()->isNull('e.estimatedDuration'));

        if ($startDateTime) {
            $builder->andWhere($builder->expr()->gte('e.dateTime', '\'' . $startDateTime->format('Y-m-d H:i:s') . '\''));
        }

        if ($endDateTime) {
            $builder->andWhere($builder->expr()->lte('e.dateTime', '\'' . $endDateTime->format('Y-m-d H:i:s') . '\''));
        }

        if ($limit) {
            $builder->setMaxResults($limit);
        }

        $builder->addOrderBy('e.dateTime', 'DESC');

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }
}
