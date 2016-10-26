<?php

namespace Caldera\Bundle\CalderaBundle\Repository;

use Doctrine\ORM\EntityRepository;

class TicketRepository extends EntityRepository
{
    public function findForQuery()
    {
        $dateTime = new \DateTime();

        $builder = $this->createQueryBuilder('ticket');

        $builder->select('ticket');

        $builder->where($builder->expr()->orX(
            $builder->expr()->eq('ticket.queried', false),
            $builder->expr()->andX(
                $builder->andWhere($builder->expr()->gte('ticket.creationDateTime', '\'' . $dateTime->format('Y-m-d H:i:s') . '\'')),
                $builder->andWhere($builder->expr()->lte('ticket.creationDateTime', '\'' . $dateTime->format('Y-m-d H:i:s') . '\''))
            )
        ));
        
        $builder->orderBy('ticket.creationDateTime', 'ASC');
    }

    public function findForTimelineLocationSharingCollector(\DateTime $startDateTime = null, \DateTime $endDateTime = null, $limit = null)
    {
        $builder = $this->createQueryBuilder('ticket');

        $builder->select('ticket');

        $builder->where($builder->expr()->orX(
            $builder->expr()->isNotNull('ticket.ride'),
            $builder->expr()->isNotNull('ticket.city')
        ));

        $builder->andWhere($builder->expr()->lte('ticket.counter', 5));

        if ($startDateTime) {
            $builder->andWhere($builder->expr()->gte('ticket.creationDateTime', '\'' . $startDateTime->format('Y-m-d H:i:s') . '\''));
        }

        if ($endDateTime) {
            $builder->andWhere($builder->expr()->lte('ticket.creationDateTime', '\'' . $endDateTime->format('Y-m-d H:i:s') . '\''));
        }

        if ($limit) {
            $builder->setMaxResults($limit);
        }

        $builder->addOrderBy('ticket.creationDateTime', 'DESC');

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }
}

