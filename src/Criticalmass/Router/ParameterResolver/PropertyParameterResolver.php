<?php declare(strict_types=1);

namespace App\Criticalmass\Router\ParameterResolver;

use App\Criticalmass\Router\Attribute\RouteParameter;
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

        foreach ($properties as $property) {
            foreach ($property->getAttributes(RouteParameter::class) as $parameterAttribute) {
                $routeParameter = $parameterAttribute->newInstance();

                if ($routeParameter->getName() !== $variableName) {
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

                if (is_object($value) && $value instanceof \DateTimeInterface) {
                    $value = $value->format($routeParameter->getDateFormat() ?? 'Y-m-d');
                }

                return (string) $value;
            }
        }

        return null;
    }
}
