<?php

namespace AppBundle\Request\ParamConverter;

use AppBundle\Entity\City;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class CityParamConverter extends AbstractCriticalmassParamConverter
{
    public function __construct(Registry $registry)
    {
        parent::__construct($registry);
    }

    public function apply(Request $request, ParamConverter $configuration): void
    {
        $city = $this->findCityById($request);

        if (!$city) {
            $city = $this->findCityBySlug($request);;
        }

        if ($city) {
            $request->attributes->set($configuration->getName(), $city);
        } else {
            $this->notFound($configuration);
        }
    }

    protected function findCityById(Request $request): ?City
    {
        $cityId = $request->get('cityId');

        if ($cityId) {
            $city = $this->registry->getRepository(City::class)->find($cityId);

            return $city;
        }

        return null;
    }
}
