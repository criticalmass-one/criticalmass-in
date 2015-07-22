<?php

namespace Caldera\CriticalmassContentBundle\Entity;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;

class ContentRepository extends EntityRepository
{
    public function findBySlug($slug)
    {
        $content = $this->findBy(array('slug' => $slug, 'enabled' => true, 'isArchived' => false));

        return $content;
    }
}

