<?php declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class CitySlugRepository extends EntityRepository
{
    public function findAllIndexed(): array
    {
        $qb = $this->createQueryBuilder('cs');
        $qb->indexBy('cs', 'cs.slug');

        return $qb->getQuery()->getResult();
    }
}
