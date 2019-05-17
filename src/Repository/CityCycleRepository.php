<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\City;
use App\Entity\Region;
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
            ->andWhere($builder->expr()->isNull('cc.disabledAt'))
            ->addOrderBy('cc.validFrom', 'DESC')
            ->addOrderBy('cc.location')
            ->setParameter('city', $city);

        if ($startDateTime) {
            $builder
                ->andWhere(
                    $builder->expr()->orX(
                        $builder->expr()->lte('cc.validFrom', ':startDateTime'),
                        $builder->expr()->isNull('cc.validFrom')
                    )
                )
                ->setParameter('startDateTime', $startDateTime);
        }

        if ($endDateTime) {
            $builder
                ->andWhere(
                    $builder->expr()->orX(
                        $builder->expr()->gte('cc.validUntil', ':endDateTime'),
                        $builder->expr()->isNull('cc.validUntil')
                    )
                )
                ->setParameter('endDateTime', $endDateTime);
        }

        $query = $builder->getQuery();

        return $query->getResult();
    }
}

