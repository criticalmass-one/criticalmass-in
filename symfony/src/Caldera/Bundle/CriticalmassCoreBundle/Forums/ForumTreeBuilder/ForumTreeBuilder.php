<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Forums\ForumTreeBuilder;

use \Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;

class ForumTreeBuilder
{
    protected $tree;
    protected $doctrine;

    public function __construct(EntityManager $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function build()
    {
        $this->doctrine->getRepository('CalderaCriticalmassModelBundle:City')->findCities();
    }

    public function getTree()
    {
        return $this->tree;
    }
}