<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\FrontpageTeaser;
use Carbon\Carbon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FrontpageTeaserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FrontpageTeaser::class);
    }

    public function findForFrontpage(): array
    {
        $dateTime = Carbon::now();
        $builder = $this->createQueryBuilder('ft');

        $builder
            ->orderBy('ft.position')
            ->andWhere(
                $builder->expr()->orX(
                    $builder->expr()->andX(
                        $builder->expr()->lte('ft.validFrom', ':dateTime'),
                        $builder->expr()->gte('ft.validUntil', ':dateTime')
                    ),
                    $builder->expr()->andX(
                        $builder->expr()->isNull('ft.validFrom'),
                        $builder->expr()->isNull('ft.validUntil')
                    )
                )
            )
            ->setParameter('dateTime', $dateTime);

        $query = $builder->getQuery();

        return $query->getResult();
    }
}

