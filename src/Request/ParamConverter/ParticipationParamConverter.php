<?php declare(strict_types=1);

namespace App\Request\ParamConverter;

use Symfony\Bridge\Doctrine\RegistryInterface;

class ParticipationParamConverter extends AbstractCriticalmassParamConverter
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry);
    }
}
