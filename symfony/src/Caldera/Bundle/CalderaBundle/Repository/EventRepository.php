<?php

namespace Caldera\Bundle\CalderaBundle\Repository;

use Caldera\Bundle\CalderaBundle\Entity\City;
use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Doctrine\ORM\EntityRepository;

class EventRepository extends EntityRepository
{
    public function findEventByCityAndSlug(City $city, $eventSlug)
    {
        $builder = $this->createQueryBuilder('event');

        $builder->select('event');

        $builder->where($builder->expr()->eq('event.city', $city->getId()));
        $builder->andWhere($builder->expr()->eq('event.slug', '\''.$eventSlug.'\''));
        $builder->andWhere($builder->expr()->eq('event.isArchived', 0));

        $query = $builder->getQuery();
        $query->setMaxResults(1);

        $result = $query->getOneOrNullResult();

        return $result;
    }

    public function findEventsByCity($city)
    {
        $builder = $this->createQueryBuilder('event');

        $builder->select('event');

        $builder->where($builder->expr()->eq('event.city', $city->getId()));
        $builder->andWhere($builder->expr()->eq('event.isArchived', 0));

        $builder->addOrderBy('event.dateTime', 'DESC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findEventsForRide(Ride $ride)
    {
        $builder = $this->createQueryBuilder('event');

        $builder->select('event');

        $builder->where($builder->expr()->eq('event.city', $ride->getCity()->getId()));
        $builder->andWhere($builder->expr()->eq('event.isArchived', 0));
        $builder->andWhere($builder->expr()->eq('DATE(event.dateTime)', '\''.$ride->getFormattedDate().'\''));

        $builder->addOrderBy('event.dateTime', 'DESC');

        $query = $builder->getQuery();

        return $query->getResult();
    }
}

