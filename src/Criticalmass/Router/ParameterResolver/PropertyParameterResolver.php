<?php declare(strict_types=1);

namespace App\Criticalmass\Router\ParameterResolver;

use App\Criticalmass\Router\Attribute\AttributeInterface;
use App\Criticalmass\Router\DelegatedRouterManager\DelegatedRouterManagerInterface;
use App\EntityInterface\RouteableInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class PropertyParameterResolver implements ParameterResolverInterface
{
    public function __construct(
        private readonly DelegatedRouterManagerInterface $delegatedRouterManager,
        private readonly ClassParameterResolver $classParameterResolver
    )
    {

    }

    public function resolve(RouteableInterface $routeable, string $variableName): ?string
    {
        $reflectionClass = new \ReflectionClass($routeable);

        $properties = $reflectionClass->getProperties();

        foreach ($properties as $key => $property) {
            $parameterAttributes = $property->getAttributes();

            /** @var AttributeInterface $parameterAttribute */
            foreach ($parameterAttributes as $parameterAttribute) {
                if ($parameterAttribute->getName() === 'App\Criticalmass\Router\Attribute\RouteParameter') {
                    if ($parameterAttribute->getArguments()['name'] !== $variableName) {
                        continue;
                    }

                    $propertyAccessor = PropertyAccess::createPropertyAccessor();

                    $value = $propertyAccessor->getValue($routeable, $property->getName());

                    if (is_object($value) && $value instanceof RouteableInterface) {
                        if ($delegatedRouter = $this->delegatedRouterManager->findDelegatedRouter($value)) {
                            $value = $delegatedRouter->getRouteParameter($value, $variableName);
                        } else {
                            return $this->classParameterResolver->resolve($value, $variableName) ?? $this->resolve($value, $variableName) ?? null;
                        }
                    }

                    if (is_object($value) && $value instanceof \DateTime) {
                        $value = $value->format($parameterAttribute->getDateFormat());
                    }

                    return (string) $value;
                }
            }
        }

        return null;
    }
}
