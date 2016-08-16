<?php

namespace Caldera\Bundle\CalderaBundle\Repository;

use Doctrine\ORM\EntityRepository;

class BlogPostRepository extends EntityRepository
{
    public function findForTimelineBlogPostCollector(\DateTime $startDateTime = null, \DateTime $endDateTime = null, $limit = null)
    {
        $builder = $this->createQueryBuilder('blogPost');

        $builder->select('blogPost');

        $builder->where($builder->expr()->eq('blogPost.enabled', 1));

        if ($startDateTime) {
            $builder->andWhere($builder->expr()->gte('blogPost.dateTime', '\''.$startDateTime->format('Y-m-d H:i:s').'\''));
        }

        if ($endDateTime) {
            $builder->andWhere($builder->expr()->lte('blogPost.dateTime', '\''.$endDateTime->format('Y-m-d H:i:s').'\''));
        }

        if ($limit) {
            $builder->setMaxResults($limit);
        }

        $builder->addOrderBy('blogPost.dateTime', 'DESC');

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }
}

