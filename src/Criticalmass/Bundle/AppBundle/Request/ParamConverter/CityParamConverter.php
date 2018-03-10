<?php

namespace Criticalmass\Bundle\AppBundle\Request\ParamConverter;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\CitySlug;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\DoctrineParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CityParamConverter extends DoctrineParamConverter
{
    public function apply(Request $request, ParamConverter $configuration): void
    {
        $city = null;

        $cityId = $request->get('cityId');

        if ($cityId) {
            $city = $this->registry->getRepository(City::class)->find($cityId);
        }

        $citySlug = $request->get('citySlug');

        if ($citySlug) {
            $cs = $this->registry->getRepository(CitySlug::class)->findOneBySlug($citySlug);

            if ($cs) {
                $city = $cs->getCity();
            }
        }

        if ($city) {
            $request->attributes->set($configuration->getName(), $city);
        } else {
            throw new NotFoundHttpException(sprintf('%s object not found.', $configuration->getClass()));
        }
    }

    public function supports(ParamConverter $configuration): bool
    {
        return $configuration->getClass() === 'AppBundle:City';
    }
}
