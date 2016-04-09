<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Repository;

use Doctrine\ORM\EntityRepository;

class TicketRepository extends EntityRepository
{
    public function findForTimelineLocationSharingCollector()
    {
        $builder = $this->createQueryBuilder('ticket');

        $builder->select('ticket');

        $builder->where($builder->expr()->orX(
            $builder->expr()->isNotNull('ticket.ride'),
            $builder->expr()->isNotNull('ticket.city')
        ));

        $builder->andWhere($builder->expr()->lte('ticket.counter', 5));

        $builder->addOrderBy('ticket.creationDateTime', 'DESC');

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }
}

