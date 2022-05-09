<?php declare(strict_types=1);

namespace App\Request\ParamConverter;

use App\Entity\City;
use App\Entity\CitySlug;
use App\EntityInterface\AutoParamConverterAble;
use Doctrine\Common\Persistence\ObjectRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class AbstractCriticalmassParamConverter extends AbstractParamConverter
{
    protected array $autoGuessOrder = ['id', 'slug'];

    public function apply(Request $request, ParamConverter $configuration): void
    {
        $object = $this->autoGuess($request, $configuration);

        if ($object) {
            $request->attributes->set($configuration->getName(), $object);
        } else {
            $this->notFound($configuration);
        }
    }

    protected function autoGuess(Request $request, ParamConverter $configuration): ?AutoParamConverterAble
    {
        foreach ($this->autoGuessOrder as $propertyName) {
            if (!$this->hasEntityPropertyName($propertyName)) {
                continue;
            }

            $requestParameterKey = sprintf('%s%s', $this->getLowercaseEntityShortName(), ucfirst($propertyName));

            if ($requestParameterValue = $request->get($requestParameterKey)) {
                $repositoryMethod = sprintf('findOneBy%s', ucfirst($propertyName));

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

        foreach ($reflectionClass->getProperties() as $property) {
            if ($property->getName() === $propertyName) {
                return true;
            }
        }

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
            return $citySlug->getCity();
        }

        return null;
    }
}
