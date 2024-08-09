<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\BlockedCity;
use App\Entity\City;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BlockedCityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlockedCity::class);
    }

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
