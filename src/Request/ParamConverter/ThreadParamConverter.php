<?php

namespace App\Request\ParamConverter;

use Symfony\Bridge\Doctrine\RegistryInterface;

class ThreadParamConverter extends AbstractCriticalmassParamConverter
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry);
    }
}
