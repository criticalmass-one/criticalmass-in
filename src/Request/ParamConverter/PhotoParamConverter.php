<?php

namespace App\Request\ParamConverter;

use Symfony\Bridge\Doctrine\RegistryInterface;

class PhotoParamConverter extends AbstractCriticalmassParamConverter
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry);
    }
}
