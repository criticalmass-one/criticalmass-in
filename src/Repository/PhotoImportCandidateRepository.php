<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\PhotoImportCandidate;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PhotoImportCandidate>
 */
class PhotoImportCandidateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PhotoImportCandidate::class);
    }

    public function findOneByUserAndFileHash(User $user, string $fileHash): ?PhotoImportCandidate
    {
        return $this->findOneBy(['user' => $user, 'fileHash' => $fileHash]);
    }

    /**
     * Active (non-rejected) candidates of a user, oldest capture first — the
     * basis for grouping into galleries on the review page.
     *
     * @return list<PhotoImportCandidate>
     */
    public function findActiveForUser(User $user): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.user = :user')
            ->andWhere('c.rejected = :rejected')
            ->setParameter('user', $user)
            ->setParameter('rejected', false)
            ->addOrderBy('c.exifCreationDate', 'ASC')
            ->addOrderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Candidates that are rejected or older than the given threshold and were never
     * confirmed (confirmed candidates are removed on import) — i.e. safe to purge.
     *
     * @return list<PhotoImportCandidate>
     */
    public function findPurgeable(\DateTimeInterface $expiredBefore): array
    {
        $builder = $this->createQueryBuilder('c');

        $builder
            ->where($builder->expr()->orX(
                $builder->expr()->eq('c.rejected', ':rejected'),
                $builder->expr()->lt('c.createdAt', ':expiredBefore'),
            ))
            ->setParameter('rejected', true)
            ->setParameter('expiredBefore', $expiredBefore)
            ->orderBy('c.createdAt', 'ASC');

        return $builder->getQuery()->getResult();
    }

    /**
     * All staged file paths still referenced by a candidate row.
     *
     * @return list<string>
     */
    public function findReferencedStagedFilenames(): array
    {
        $builder = $this->createQueryBuilder('c');

        $builder
            ->select('c.stagedFilename')
            ->where($builder->expr()->isNotNull('c.stagedFilename'));

        return array_column($builder->getQuery()->getScalarResult(), 'stagedFilename');
    }
}
