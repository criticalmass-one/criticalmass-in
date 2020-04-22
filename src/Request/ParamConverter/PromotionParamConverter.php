<?php declare(strict_types=1);

namespace App\Request\ParamConverter;

use Doctrine\Persistence\ManagerRegistry;

class PromotionParamConverter extends AbstractCriticalmassParamConverter
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry);
    }
}
