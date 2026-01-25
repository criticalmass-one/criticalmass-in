<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\City;
use App\Entity\CityCycle;
use App\Entity\Region;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CityCycleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CityCycle::class);
    }

    public function findByCity(
        City $city,
        ?\DateTimeInterface $startDateTime = null,
        ?\DateTimeInterface $endDateTime = null
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

    public function findForApi(
        ?City $city = null,
        ?Region $region = null,
        ?\DateTimeInterface $startDateTime = null,
        ?\DateTimeInterface $endDateTime = null,
        ?bool $validNow = null,
        ?int $dayOfWeek = null,
        ?int $weekOfMonth = null
    ): array
    {
        $builder = $this->createQueryBuilder('cc');

        $builder
            ->where($builder->expr()->isNull('cc.disabledAt'))
            ->join('cc.city', 'c');

        if ($city) {
            $builder
                ->andWhere($builder->expr()->eq('cc.city', ':city'))
                ->setParameter('city', $city);
        }

        if ($region) {
            $builder
                ->andWhere($builder->expr()->eq('c.region', ':region'))
                ->setParameter('region', $region);
        }

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

        if ($dayOfWeek) {
            $builder
                ->andWhere($builder->expr()->eq('cc.dayOfWeek', ':dayOfWeek'))
                ->setParameter('dayOfWeek', $dayOfWeek);
        }

        if ($weekOfMonth) {
            $builder
                ->andWhere($builder->expr()->eq('cc.weekOfMonth', ':weekOfMonth'))
                ->setParameter('weekOfMonth', $weekOfMonth);
        }

        $builder
            ->addOrderBy('c.city', 'ASC')
            ->addOrderBy('cc.location', 'ASC');

        $query = $builder->getQuery();

        return $query->getResult();
    }
}
