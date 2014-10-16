<?php

namespace Caldera\CriticalmassTimelineBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PostRepository extends EntityRepository
{
    public function countPosts()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('COUNT(post.id)');
        $qb->from('CalderaCriticalmassTimelineBundle:Post', 'post');

        return $qb->getQuery()->getSingleScalarResult();
    }
}

