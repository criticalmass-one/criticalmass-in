<?php declare(strict_types=1);

namespace App\Request\ParamConverter;

use Symfony\Bridge\Doctrine\RegistryInterface;

class LocationParamConverter extends AbstractCriticalmassParamConverter
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry);
    }
}
