<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Repository;

use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Doctrine\ORM\EntityRepository;

class PhotoRepository extends EntityRepository
{
    public function getPhotosForRide(Ride $ride)
    {
        $builder = $this->createQueryBuilder('photo');
        
        $builder->select('photo');

        $builder->where($builder->expr()->eq('photo.ride', $ride));
        $builder->where($builder->expr()->eq('photo.enabled', true));
        
        $builder->addOrderBy('photo.dateTime', 'ASC');
        
        $query = $builder->getQuery();
        
        $result = $query->getResult();
        
        return $result;
    }
}

