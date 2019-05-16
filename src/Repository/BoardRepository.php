<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Board;
use Doctrine\ORM\EntityRepository;

class BoardRepository extends EntityRepository
{
    public function findEnabledBoards(): array
    {
        $builder = $this->createQueryBuilder('b');

        $builder
            ->select('b')
            ->where($builder->expr()->eq('b.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->orderBy('b.position', 'ASC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findBoardBySlug(string $slug): ?Board
    {
        $builder = $this->createQueryBuilder('b');

        $builder
            ->select('b')
            ->where($builder->expr()->eq('b.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->andWhere($builder->expr()->eq('b.slug', ':slug'))
            ->setParameter('slug', $slug);

        $query = $builder->getQuery();

        return $query->getOneOrNullResult();
    }
}
