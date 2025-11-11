<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\CitySlug;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CitySlugRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CitySlug::class);
    }

    public function findAllIndexed(): array
    {
        $qb = $this->createQueryBuilder('cs');
        $qb->indexBy('cs', 'cs.slug');

        return $qb->getQuery()->getResult();
    }
}
