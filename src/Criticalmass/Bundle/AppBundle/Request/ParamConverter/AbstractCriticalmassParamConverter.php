<?php

namespace Criticalmass\Bundle\AppBundle\Request\ParamConverter;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\CitySlug;
use Doctrine\Common\Persistence\ObjectRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class AbstractCriticalmassParamConverter extends AbstractParamConverter
{
    protected $autoGuessOrder = ['id', 'slug'];

    public function apply(Request $request, ParamConverter $configuration): void
    {
        $object = $this->autoGuess($request, $configuration);

        if ($object) {
            $request->attributes->set($configuration->getName(), $object);
        } else {
            $this->notFound($configuration);
        }
    }

    protected function autoGuess(Request $request, ParamConverter $configuration)
    {
        foreach ($this->autoGuessOrder as $property) {
            $requestParameterKey = sprintf('%s%s', $this->getLowercaseEntityShortName(), ucfirst($property));

            if ($requestParameterValue = $request->get($requestParameterKey)) {
                $repositoryMethod = sprintf('findOneBy%s', ucfirst($property));

                return $this->getRepository()->$repositoryMethod($requestParameterValue);
            }
        }

        return null;
    }

    protected function getRepository(): ObjectRepository
    {
        return $this->registry->getRepository($this->getEntityFqcn());
    }

    protected function hasEntityPropertyName(string $propertyName): bool
    {
        $reflectionClass = new \ReflectionClass($this->getEntityFqcn());

        var_dump($reflectionClass->getProperties());

        return false;
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
}
