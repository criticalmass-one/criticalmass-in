<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Ride;
use App\Entity\Subride;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SubrideRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subride::class);
    }

    public function getSubridesForRide(Ride $ride): array
    {
        $builder = $this->createQueryBuilder('sr');

        $builder
            ->select('sr')
            ->where($builder->expr()->eq('sr.ride', ':ride'))
            ->addOrderBy('sr.dateTime', 'ASC')
            ->setParameter('ride', $ride);

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }
}

