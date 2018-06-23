<?php

namespace AppBundle\Request\ParamConverter;

use Doctrine\Bundle\DoctrineBundle\Registry;

class LocationParamConverter extends AbstractCriticalmassParamConverter
{
    public function __construct(Registry $registry)
    {
        parent::__construct($registry);
    }
}
