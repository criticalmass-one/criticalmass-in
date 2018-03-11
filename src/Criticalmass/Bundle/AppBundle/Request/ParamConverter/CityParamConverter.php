<?php

namespace Criticalmass\Bundle\AppBundle\Request\ParamConverter;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CityParamConverter extends AbstractParamConverter
{
    public function apply(Request $request, ParamConverter $configuration): void
    {
        $city = null;

        $cityId = $request->get('cityId');

        if ($cityId) {
            $city = $this->registry->getRepository(City::class)->find($cityId);
        }

        if ($city) {
            $request->attributes->set($configuration->getName(), $city);

            return;
        }

        $citySlug = $request->get('citySlug');

        $city = $this->findCityBySlug($citySlug);

        if ($city) {
            $request->attributes->set($configuration->getName(), $city);
        } else {
            throw new NotFoundHttpException(sprintf('%s object not found.', $configuration->getClass()));
        }
    }
}
