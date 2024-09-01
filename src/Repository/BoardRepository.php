<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Board;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BoardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Board::class);
    }

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
