<?php

namespace Caldera\Bundle\CalderaBundle\Repository;

use Doctrine\ORM\EntityRepository;

class RideEstimateRepository extends EntityRepository
{
    public function findForTimelineRideParticipantsEstimateCollector(\DateTime $startDateTime = null, \DateTime $endDateTime = null, $limit = null)
    {
        $builder = $this->createQueryBuilder('estimate');

        $builder->select('estimate');
        $builder->where($builder->expr()->isNull('estimate.track'));
        $builder->andWhere($builder->expr()->isNull('estimate.estimatedDistance'));
        $builder->andWhere($builder->expr()->isNull('estimate.estimatedDuration'));

        if ($startDateTime) {
            $builder->andWhere($builder->expr()->gte('estimate.creationDateTime', '\'' . $startDateTime->format('Y-m-d H:i:s') . '\''));
        }

        if ($endDateTime) {
            $builder->andWhere($builder->expr()->lte('estimate.creationDateTime', '\'' . $endDateTime->format('Y-m-d H:i:s') . '\''));
        }

        if ($limit) {
            $builder->setMaxResults($limit);
        }

        $builder->addOrderBy('estimate.creationDateTime', 'DESC');

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }
}