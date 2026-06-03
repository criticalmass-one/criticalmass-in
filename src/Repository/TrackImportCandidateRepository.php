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

    /**
     * Upload candidates of the user that have been matched to a ride and await confirmation.
     *
     * @return TrackImportCandidate[]
     */
    public function findMatchedUploadCandidatesForUser(User $user): array
    {
        $builder = $this->createQueryBuilder('tic');

        $builder
            ->where($builder->expr()->eq('tic.rejected', ':rejected'))
            ->setParameter('rejected', false)
            ->andWhere($builder->expr()->eq('tic.user', ':user'))
            ->setParameter('user', $user)
            ->andWhere($builder->expr()->eq('tic.source', ':source'))
            ->setParameter('source', TrackImportCandidate::CANDIDATE_SOURCE_UPLOAD)
            ->join('tic.ride', 'r')
            ->orderBy('r.dateTime', 'ASC');

        return $builder->getQuery()->getResult();
    }

    /**
     * Upload candidates of the user that could not be matched to a ride (parked for review).
     *
     * @return TrackImportCandidate[]
     */
    public function findParkedUploadCandidatesForUser(User $user): array
    {
        $builder = $this->createQueryBuilder('tic');

        $builder
            ->where($builder->expr()->eq('tic.rejected', ':rejected'))
            ->setParameter('rejected', false)
            ->andWhere($builder->expr()->eq('tic.user', ':user'))
            ->setParameter('user', $user)
            ->andWhere($builder->expr()->eq('tic.source', ':source'))
            ->setParameter('source', TrackImportCandidate::CANDIDATE_SOURCE_UPLOAD)
            ->andWhere($builder->expr()->isNull('tic.ride'))
            ->orderBy('tic.createdAt', 'DESC');

        return $builder->getQuery()->getResult();
    }

    /**
     * Upload candidates that are rejected or older than the given threshold and were never
     * confirmed (confirmed candidates are removed on import). Strava candidates are excluded.
     *
     * @return TrackImportCandidate[]
     */
    public function findPurgeableUploadCandidates(\DateTimeInterface $expiredBefore): array
    {
        $builder = $this->createQueryBuilder('tic');

        $builder
            ->where($builder->expr()->eq('tic.source', ':source'))
            ->setParameter('source', TrackImportCandidate::CANDIDATE_SOURCE_UPLOAD)
            ->andWhere($builder->expr()->orX(
                $builder->expr()->eq('tic.rejected', ':rejected'),
                $builder->expr()->lt('tic.createdAt', ':expiredBefore'),
            ))
            ->setParameter('rejected', true)
            ->setParameter('expiredBefore', $expiredBefore)
            ->orderBy('tic.createdAt', 'ASC');

        return $builder->getQuery()->getResult();
    }

    /**
     * All stored upload-candidate file paths still referenced by a candidate row.
     *
     * @return string[]
     */
    public function findReferencedUploadFilenames(): array
    {
        $builder = $this->createQueryBuilder('tic');

        $builder
            ->select('tic.trackFilename')
            ->where($builder->expr()->eq('tic.source', ':source'))
            ->setParameter('source', TrackImportCandidate::CANDIDATE_SOURCE_UPLOAD)
            ->andWhere($builder->expr()->isNotNull('tic.trackFilename'));

        return array_column($builder->getQuery()->getScalarResult(), 'trackFilename');
    }
}
