<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Repository;

use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
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
}

