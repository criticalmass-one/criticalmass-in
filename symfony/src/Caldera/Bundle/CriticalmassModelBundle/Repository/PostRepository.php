<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PostRepository extends EntityRepository
{
    public function countPosts()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('COUNT(post.id)');
        $qb->from('CalderaCriticalmassModelBundle:Post', 'post');

        return $qb->getQuery()->getSingleScalarResult();
    }
}

