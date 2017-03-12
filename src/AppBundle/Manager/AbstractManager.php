<?php

namespace Caldera\Bundle\CalderaBundle\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;

abstract class AbstractManager
{
    /** @var Registry $doctrine */
    protected $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
}