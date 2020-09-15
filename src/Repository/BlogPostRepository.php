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

    public function findForTimelineBlogPostCollector(\DateTime $startDateTime = null, \DateTime $endDateTime = null, $limit = null): array
    {
        $builder = $this->createQueryBuilder('bp');

        $builder
            ->select('bp')
            ->where($builder->expr()->eq('bp.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->addOrderBy('bp.createdAt', 'DESC');

        if ($startDateTime) {
            $builder
                ->andWhere($builder->expr()->gte('bp.createdAt',':startDateTime'))
                ->setParameter('startDateTime', $startDateTime);
        }

        if ($endDateTime) {
            $builder
                ->andWhere($builder->expr()->lte('bp.createdAt', ':endDateTime'))
                ->setParameter('endDateTime', $endDateTime);
        }

        if ($limit) {
            $builder->setMaxResults($limit);
        }

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }
}
