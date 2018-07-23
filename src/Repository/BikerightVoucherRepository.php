<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\BikerightVoucher;
use Doctrine\ORM\EntityRepository;

class BikerightVoucherRepository extends EntityRepository
{
    public function findUnassignedVoucher(): ?BikerightVoucher
    {
        $qb = $this->createQueryBuilder('bv');

        $qb
            ->where($qb->expr()->isNull('bv.user'))
            ->orderBy('bv.priority', 'DESC')
            ->setMaxResults(1);

        $query = $qb->getQuery();

        return $query->getOneOrNullResult();
    }
}

