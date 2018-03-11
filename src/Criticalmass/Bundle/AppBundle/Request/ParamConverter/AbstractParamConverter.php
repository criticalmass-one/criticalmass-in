<?php

namespace Criticalmass\Bundle\AppBundle\Request\ParamConverter;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\CitySlug;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\DoctrineParamConverter;

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

    protected function findCitySlug(string $citySlug): ?CitySlug
    {
        return $this->registry->getRepository(CitySlug::class)->findOneBySlug($citySlug);
    }

    protected function findCityBySlug(string $citySlug): ?City
    {
        $cs = $this->findCitySlug($citySlug);

        if ($cs) {
            $city = $cs->getCity();

            return $city;
        }

        return null;
    }
}
