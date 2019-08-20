<?php

namespace App\Repository;

use App\Entity\HeatmapTrack;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method HeatmapTrack|null find($id, $lockMode = null, $lockVersion = null)
 * @method HeatmapTrack|null findOneBy(array $criteria, array $orderBy = null)
 * @method HeatmapTrack[]    findAll()
 * @method HeatmapTrack[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HeatmapTrackRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, HeatmapTrack::class);
    }

    // /**
    //  * @return HeatmapTrack[] Returns an array of HeatmapTrack objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?HeatmapTrack
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
