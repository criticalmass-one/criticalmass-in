<?php

namespace Caldera\Bundle\CalderaBundle\Manager;

abstract class AbstractManager
{
    protected $doctrine;

    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }
}