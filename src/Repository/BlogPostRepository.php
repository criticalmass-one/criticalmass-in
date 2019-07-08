<?php declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class BlogPostRepository extends EntityRepository
{
    public function findForBlogFrontpage(): array
    {
        $qb = $this->createQueryBuilder('bp');
        $qb
            ->where($qb->expr()->eq('bp.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->orderBy('bp.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }
}
