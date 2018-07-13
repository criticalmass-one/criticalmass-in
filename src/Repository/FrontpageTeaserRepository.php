<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\City;
use Doctrine\ORM\EntityRepository;

class FrontpageTeaserRepository extends EntityRepository
{
    public function findForFrontpage(): array
    {
        $dateTime = new \DateTime();
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

