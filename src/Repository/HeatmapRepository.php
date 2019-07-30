<?php

namespace App\Repository;

use App\Entity\Heatmap;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Heatmap|null find($id, $lockMode = null, $lockVersion = null)
 * @method Heatmap|null findOneBy(array $criteria, array $orderBy = null)
 * @method Heatmap[]    findAll()
 * @method Heatmap[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HeatmapRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Heatmap::class);
    }

    // /**
    //  * @return Heatmap[] Returns an array of Heatmap objects
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
    public function findOneBySomeField($value): ?Heatmap
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
