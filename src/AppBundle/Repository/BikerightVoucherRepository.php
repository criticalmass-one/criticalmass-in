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
            ->setMaxResults(1)
        ;

        $query = $qb->getQuery();

        return $query->getOneOrNullResult();
    }
}

