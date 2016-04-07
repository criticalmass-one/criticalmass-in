<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Repository;

use Doctrine\ORM\EntityRepository;

class RideEstimateRepository extends EntityRepository
{
    public function findForTimelineRideParticipantsEstimateCollector()
    {
        $builder = $this->createQueryBuilder('estimate');

        $builder->select('estimate');
        $builder->where($builder->expr()->isNull('estimate.track'));
        $builder->andWhere($builder->expr()->isNull('estimate.estimatedDistance'));
        $builder->andWhere($builder->expr()->isNull('estimate.estimatedDuration'));

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }
}