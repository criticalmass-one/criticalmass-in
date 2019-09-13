<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;

class TrackImportCandidateRepository extends EntityRepository
{
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
