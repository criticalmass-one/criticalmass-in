<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Repository;

use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassModelBundle\Entity\CriticalmapsUser;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Doctrine\ORM\EntityRepository;
use \Caldera\Bundle\CriticalmassModelBundle\Entity\Thread;

class PositionRepository extends EntityRepository
{
    public function findFirstPositionForCriticalmapsUser(CriticalmapsUser $cmu)
    {
        $builder = $this->createQueryBuilder('position');

        $builder->where($builder->expr()->eq('position.criticalmapsUser', $cmu->getId()));
        $builder->addOrderBy('position.creationDateTime');
        $builder->setMaxResults(1);

        $query = $builder->getQuery();

        return $query->getOneOrNullResult();
    }
}

