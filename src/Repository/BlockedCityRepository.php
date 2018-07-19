<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\City;
use Doctrine\ORM\EntityRepository;

class BlockedCityRepository extends EntityRepository
{
    public function findCurrentCityBlock(City $city)
    {
        $builder = $this->createQueryBuilder('cb');

        $builder->select('cb');

        $builder->where($builder->expr()->eq('cb.city', $city->getId()));

        $query = $builder->getQuery();

        return $query->getOneOrNullResult();
    }

}

