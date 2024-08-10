<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\TrackImportCandidate;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TrackImportCandidateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrackImportCandidate::class);
    }

    public function findCandidatesForUser(User $user): array
    {
        $builder = $this->createQueryBuilder('tic');

        $builder
            ->where($builder->expr()->eq('tic.rejected', ':rejected'))
            ->setParameter('rejected', false)
            ->andWhere($builder->expr()->eq('tic.user', ':user'))
            ->setParameter('user', $user)
            ->join('tic.ride', 'r')
            ->orderBy('r.dateTime', 'ASC');

        $query = $builder->getQuery();

        return $query->getResult();
    }
}
