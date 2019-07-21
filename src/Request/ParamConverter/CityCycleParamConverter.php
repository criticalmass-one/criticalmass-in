<?php declare(strict_types=1);

namespace App\Request\ParamConverter;

use App\Entity\CityCycle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;

class CityCycleParamConverter extends AbstractCriticalmassParamConverter
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry);
    }

    public function apply(Request $request, ParamConverter $configuration): void
    {
        $cityCycleId = $request->get('cityCycleId');
        $cityCycle = null;

        if ($cityCycleId) {
            $cityCycle = $this->registry->getRepository(CityCycle::class)->find($cityCycleId);
        }

        if ($cityCycle) {
            $request->attributes->set($configuration->getName(), $cityCycle);
        } else {
            $this->notFound($configuration);
        }
    }
}
