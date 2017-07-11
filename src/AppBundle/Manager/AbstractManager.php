<?php

namespace AppBundle\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * @deprecated
 */
abstract class AbstractManager
{
    /** @var Registry $doctrine */
    protected $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
}