<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Alert;
use Carbon\Carbon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AlertRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Alert::class);
    }

    public function findCurrentAlerts(): array
    {
        $qb = $this->createQueryBuilder('a');

        $qb->where($qb->expr()->lte('a.fromDateTime', ':dateTime'))
            ->andWhere($qb->expr()->gte('a.untilDateTime', ':dateTime'))
            ->setParameter('dateTime', Carbon::now());

        $query = $qb->getQuery();

        return $query->getArrayResult();
    }
}
