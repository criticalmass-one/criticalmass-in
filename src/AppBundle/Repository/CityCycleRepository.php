<?php

namespace AppBundle\Repository;

use AppBundle\Entity\City;
use AppBundle\Entity\Region;
use Doctrine\ORM\EntityRepository;

class CityCycleRepository extends EntityRepository
{
    public function findByCity(City $city): array
    {
        $builder = $this->createQueryBuilder('cc');

        $builder
            ->where($builder->expr()->eq('cc.city', ':city'))
            ->addOrderBy('cc.validFrom')
            ->addOrderBy('cc.location')
            ->setParameter('city', $city)
        ;

        $query = $builder->getQuery();

        return $query->getResult();
    }
}

