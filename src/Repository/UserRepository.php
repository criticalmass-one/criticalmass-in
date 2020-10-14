<?php declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findWithoutProfilePhoto(): array
    {
        $builder = $this->createQueryBuilder('u');

        $builder->where($builder->expr()->isNull('u.imageName'));

        $query = $builder->getQuery();

        return $query->getResult();
    }
}

