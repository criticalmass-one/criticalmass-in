<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\City;
use App\Entity\CityActivity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<CityActivity> */
class CityActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CityActivity::class);
    }

    public function findLatestByCity(City $city): ?CityActivity
    {
        return $this->createQueryBuilder('ca')
            ->where('ca.city = :city')
            ->setParameter('city', $city)
            ->orderBy('ca.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /** @return list<CityActivity> */
    public function findByCity(City $city, int $limit = 10): array
    {
        return $this->createQueryBuilder('ca')
            ->where('ca.city = :city')
            ->setParameter('city', $city)
            ->orderBy('ca.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
