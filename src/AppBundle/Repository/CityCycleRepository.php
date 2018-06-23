<?php

namespace AppBundle\Repository;

use AppBundle\Entity\City;
use AppBundle\Entity\Region;
use Doctrine\ORM\EntityRepository;

class CityCycleRepository extends EntityRepository
{
    public function findByCity(
        City $city,
        \DateTimeInterface $startDateTime = null,
        \DateTimeInterface $endDateTime = null
    ): array {
        $builder = $this->createQueryBuilder('cc');

        $builder
            ->where($builder->expr()->eq('cc.city', ':city'))
            ->addOrderBy('cc.validFrom', 'DESC')
            ->addOrderBy('cc.location')
            ->setParameter('city', $city);

        if ($startDateTime) {
            $builder
                ->andWhere(
                    $builder->expr()->orX(
                        $builder->expr()->andX(
                            $builder->expr()->lte('cc.validFrom', ':startDateTime'),
                            $builder->expr()->gte('cc.validUntil', ':startDateTime')
                        ),
                        $builder->expr()->andX(
                            $builder->expr()->isNull('cc.validFrom'),
                            $builder->expr()->isNull('cc.validUntil')
                        )
                    )
                )
                ->setParameter('startDateTime', $startDateTime);
        }

        if ($endDateTime) {
            $builder
                ->andWhere(
                    $builder->expr()->orX(
                        $builder->expr()->andX(
                            $builder->expr()->lte('cc.validFrom', ':endDateTime'),
                            $builder->expr()->gte('cc.validUntil', ':endDateTime')
                        ),
                        $builder->expr()->andX(
                            $builder->expr()->isNull('cc.validFrom'),
                            $builder->expr()->isNull('cc.validUntil')
                        )
                    )
                )
                ->setParameter('endDateTime', $endDateTime);
        }

        $query = $builder->getQuery();

        return $query->getResult();
    }
}

