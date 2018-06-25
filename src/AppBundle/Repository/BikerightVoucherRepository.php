<?php

namespace AppBundle\Repository;

use AppBundle\Entity\BikerightVoucher;
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

