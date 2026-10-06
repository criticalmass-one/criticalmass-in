<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Post;
use App\Entity\PostReport;
use App\Entity\User;
use App\Enum\PostReportStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PostReport>
 */
class PostReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostReport::class);
    }

    /**
     * Offene Meldungen, nach Beitrag gebuendelt, aelteste Meldung zuerst.
     *
     * @return array<int, array{post: Post, reports: list<PostReport>}>
     */
    public function findOpenGroupedByPost(): array
    {
        /** @var list<PostReport> $reports */
        $reports = $this->createQueryBuilder('r')
            ->join('r.post', 'p')
            ->addSelect('p')
            ->where('r.status = :open')
            ->setParameter('open', PostReportStatusEnum::OPEN)
            ->orderBy('r.createdAt', 'ASC')
            ->addOrderBy('r.id', 'ASC')
            ->getQuery()
            ->getResult();

        $grouped = [];

        foreach ($reports as $report) {
            $postId = (int) $report->getPost()->getId();
            $grouped[$postId] ??= ['post' => $report->getPost(), 'reports' => []];
            $grouped[$postId]['reports'][] = $report;
        }

        return $grouped;
    }

    /**
     * @return list<PostReport>
     */
    public function findOpenForPost(Post $post): array
    {
        return $this->findBy(['post' => $post, 'status' => PostReportStatusEnum::OPEN], ['createdAt' => 'ASC']);
    }

    public function hasOpenReportBy(Post $post, User $reporter): bool
    {
        return null !== $this->findOneBy([
            'post' => $post,
            'reporter' => $reporter,
            'status' => PostReportStatusEnum::OPEN,
        ]);
    }

    public function countOpen(): int
    {
        return $this->count(['status' => PostReportStatusEnum::OPEN]);
    }
}
