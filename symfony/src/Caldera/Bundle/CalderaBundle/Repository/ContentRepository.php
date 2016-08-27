<?php

namespace Caldera\Bundle\CalderaBundle\Repository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;

class ContentRepository extends EntityRepository
{
    public function findBySlug($slug)
    {
        $content = $this->findOneBy(
            [
                'slug' => $slug,
                'enabled' => true,
                'isArchived' => false
            ]
        );

        return $content;
    }

    public function findEnabledContent()
    {
        $result = $this->findBy(
            [
                'enabled' => true,
                'isArchived' => false
            ]
        );

        return $result;
    }
}

