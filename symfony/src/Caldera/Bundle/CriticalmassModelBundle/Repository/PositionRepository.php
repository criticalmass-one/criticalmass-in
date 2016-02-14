<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Repository;

use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassModelBundle\Entity\CriticalmapsUser;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ticket;
use Doctrine\ORM\EntityRepository;
use \Caldera\Bundle\CriticalmassModelBundle\Entity\Thread;

class PositionRepository extends EntityRepository
{
    public function findFirstPositionForCriticalmapsUser(CriticalmapsUser $cmu)
    {
        $builder = $this->createQueryBuilder('position');

        $builder->where($builder->expr()->eq('position.criticalmapsUser', $cmu->getId()));
        $builder->addOrderBy('position.creationDateTime', 'ASC');
        $builder->setMaxResults(1);

        $query = $builder->getQuery();

        return $query->getOneOrNullResult();
    }

    public function findPositionsForTicket(Ticket $ticket)
    {
        $builder = $this->createQueryBuilder('position');

        $builder->where($builder->expr()->eq('position.ticket', $ticket->getId()));
        $builder->addOrderBy('position.timestamp', 'ASC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findPositionsForCriticalmapsUser(CriticalmapsUser $cmu)
    {
        $builder = $this->createQueryBuilder('position');

        $builder->where($builder->expr()->eq('position.criticalmapsUser', $cmu->getId()));
        $builder->addOrderBy('position.creationDateTime', 'ASC');

        $query = $builder->getQuery();

        return $query->getResult();
    }
}

