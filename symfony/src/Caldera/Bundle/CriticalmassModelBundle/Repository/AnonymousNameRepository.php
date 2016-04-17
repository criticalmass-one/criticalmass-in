<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Repository;

use Doctrine\ORM\EntityRepository;

class AnonymousNameRepository extends EntityRepository
{
    public function findOneRandomUnusedName($sex = 'male', $locale = 'de')
    {
        $builder = $this->createQueryBuilder('name');

        $builder->select('name');

        $builder->leftJoin('name.posts', 'posts');

        $builder->where($builder->expr()->eq('name.enabled', 1));
        $builder->andWhere($builder->expr()->eq('name.locale', '\''.$locale.'\''));
        $builder->andWhere($builder->expr()->eq('name.sex', '\''.$sex.'\''));

        $builder->orderBy('posts.dateTime', 'ASC');

        $query = $builder->getQuery();

        $result = $query->getResult();

        $randomKey = array_rand($result);
        
        return $result[$randomKey];
    }
}

