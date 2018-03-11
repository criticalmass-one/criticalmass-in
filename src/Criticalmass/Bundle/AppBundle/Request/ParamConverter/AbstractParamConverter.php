<?php

namespace Criticalmass\Bundle\AppBundle\Request\ParamConverter;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\CitySlug;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\DoctrineParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class AbstractParamConverter extends DoctrineParamConverter
{
    public function supports(ParamConverter $configuration): bool
    {
        $shortname = $this->getEntityShortName();
        $longname = sprintf('AppBundle:%s', $shortname);

        return $configuration->getClass() === $longname;
    }

    protected function getEntityShortName(): ?string
    {
        $reflection = new \ReflectionClass($this);

        preg_match('/([A-z]+)ParamConverter/', $reflection->getShortName(), $matches);

        return array_pop($matches);
    }

    protected function findCityBySlug(Request $request): ?City
    {
        $citySlugString = $request->get('citySlug');

        if (!$citySlugString) {
            return null;
        }

        $citySlug = $this->registry->getRepository(CitySlug::class)->findOneBySlug($citySlugString);

        if (!$citySlug) {
            return null;
        }

        if ($citySlug) {
            $city = $citySlug->getCity();

            return $city;
        }

        return null;
    }

    protected function notFound(ParamConverter $configuration): void
    {
        if (!$configuration->isOptional()) {
            throw new NotFoundHttpException(sprintf('%s object not found.', $configuration->getClass()));
        }
    }
}
