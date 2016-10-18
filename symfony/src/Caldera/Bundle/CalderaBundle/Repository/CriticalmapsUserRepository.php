<?php

namespace Caldera\Bundle\CalderaBundle\Repository;

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

    public function findForTimelineLocationSharingCollector(\DateTime $startDateTime = null, \DateTime $endDateTime = null, $limit = null)
    {
        $builder = $this->createQueryBuilder('criticalmapsuser');

        $builder->select('criticalmapsuser');

        $builder->where($builder->expr()->orX(
            $builder->expr()->isNotNull('criticalmapsuser.ride'),
            $builder->expr()->isNotNull('criticalmapsuser.city')
        ));

        if ($startDateTime) {
            $builder->andWhere($builder->expr()->gte('criticalmapsuser.creationDateTime', '\'' . $startDateTime->format('Y-m-d H:i:s') . '\''));
        }

        if ($endDateTime) {
            $builder->andWhere($builder->expr()->lte('criticalmapsuser.creationDateTime', '\'' . $endDateTime->format('Y-m-d H:i:s') . '\''));
        }

        if ($limit) {
            $builder->setMaxResults($limit);
        }

        $builder->addOrderBy('criticalmapsuser.creationDateTime', 'DESC');

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }
}

