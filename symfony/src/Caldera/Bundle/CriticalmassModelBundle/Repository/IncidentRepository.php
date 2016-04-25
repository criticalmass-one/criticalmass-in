<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Repository;

use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Caldera\Bundle\CriticalmassModelBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class IncidentRepository extends EntityRepository
{
    public function findByRide(Ride $ride)
    {
        $builder = $this->createQueryBuilder('incident');

        $builder->select('incident');
        $builder->where($builder->expr()->eq('incident.city', $ride->getCity()->getId()));

        $builder->andWhere($builder->expr()->lte('incident.visibleFrom', '\''.$ride->getDateTime()->format('Y-m-d H:i:s').'\''));

        $builder->andWhere($builder->expr()->gte('incident.visibleTo', '\''.$ride->getDateTime()->format('Y-m-d H:i:s').'\''));

        $query = $builder->getQuery();

        return $query->getResult();
    }
}

