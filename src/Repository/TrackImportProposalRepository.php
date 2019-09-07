<?php

namespace App\Repository;

use App\Entity\TrackImportProposal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TrackImportProposal|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrackImportProposal|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrackImportProposal[]    findAll()
 * @method TrackImportProposal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrackImportProposalRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TrackImportProposal::class);
    }

    // /**
    //  * @return TrackImportProposal[] Returns an array of TrackImportProposal objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TrackImportProposal
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
