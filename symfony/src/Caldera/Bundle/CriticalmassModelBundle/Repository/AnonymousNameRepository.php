<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Repository;

use Doctrine\ORM\EntityRepository;

class AnonymousNameRepository extends EntityRepository
{
    public function findOneRandomUnusedName()
    {
        $builder = $this->createQueryBuilder('name');

        $builder->select('name');

        $builder->where($builder->expr()->eq('name.enabled', 1));
        $builder->andWhere($builder->expr()->eq('name.locale', '\'de\''));

        $query = $builder->getQuery();

        $result = $query->getResult();

        $randomKey = array_rand($result);

        return $result[$randomKey];
    }
}

