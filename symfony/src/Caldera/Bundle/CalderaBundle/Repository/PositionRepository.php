<?php

namespace Caldera\Bundle\CalderaBundle\Repository;

use Caldera\Bundle\CalderaBundle\Entity\City;
use Caldera\Bundle\CalderaBundle\Entity\CriticalmapsUser;
use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Caldera\Bundle\CalderaBundle\Entity\Ticket;
use Doctrine\ORM\EntityRepository;
use \Caldera\Bundle\CalderaBundle\Entity\Thread;

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

