<?php declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class AlertRepository extends EntityRepository
{
    public function findCurrentAlerts(): array
    {
        $qb = $this->createQueryBuilder('a');

        $qb->where($qb->expr()->lte('a.fromDateTime', ':dateTime'))
            ->andWhere($qb->expr()->gte('a.untilDateTime', ':dateTime'))
            ->setParameter('dateTime', new \DateTime());

        $query = $qb->getQuery();

        return $query->getArrayResult();
    }
}
