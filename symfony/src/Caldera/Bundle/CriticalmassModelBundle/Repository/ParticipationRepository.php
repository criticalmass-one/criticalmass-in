<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Repository;

use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Doctrine\ORM\EntityRepository;
use FOS\UserBundle\Entity\User;

class ParticipationRepository extends EntityRepository
{
    public function findParticipationForUserAndRide(User $user, Ride $ride)
    {
        $builder = $this->createQueryBuilder('participation');

        $builder->select('participation');
        $builder->where($builder->expr()->eq('participation.user', $user->getId()));
        $builder->andWhere($builder->expr()->eq('participation.ride', $ride->getId()));
        $builder->setMaxResults(1);

        $query = $builder->getQuery();

        return $query->getOneOrNullResult();
    }
}

