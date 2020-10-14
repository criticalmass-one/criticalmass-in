<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\BlockedCity;
use App\Entity\City;
use Doctrine\ORM\EntityRepository;

class BlockedCityRepository extends EntityRepository
{
    public function findCurrentCityBlock(City $city): ?BlockedCity
    {
        $builder = $this->createQueryBuilder('cb');

        $builder
            ->select('cb')
            ->where($builder->expr()->eq('cb.city', ':city'))
            ->setParameter('city', $city);

        $query = $builder->getQuery();

        return $query->getOneOrNullResult();
    }
}
