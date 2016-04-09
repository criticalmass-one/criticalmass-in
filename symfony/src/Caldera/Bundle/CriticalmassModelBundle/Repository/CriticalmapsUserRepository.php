<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CriticalmapsUserRepository extends EntityRepository
{
    public function findNotExportedAssignedUsers()
    {
        $builder = $this->createQueryBuilder('criticalmapsuser');

        $builder->select('criticalmapsuser');

        $builder->where($builder->expr()->eq('criticalmapsuser.exported', 0));
        $builder->andWhere($builder->expr()->isNotNull('criticalmapsuser.ride'));
        $builder->addOrderBy('criticalmapsuser.creationDateTime', 'ASC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findForTimelineLocationSharingCollector()
    {
        $builder = $this->createQueryBuilder('criticalmapsuser');

        $builder->select('criticalmapsuser');

        $builder->where($builder->expr()->orX(
            $builder->expr()->isNotNull('criticalmapsuser.ride'),
            $builder->expr()->isNotNull('criticalmapsuser.city')
        ));

        $builder->addOrderBy('criticalmapsuser.creationDateTime', 'DESC');

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }
}

