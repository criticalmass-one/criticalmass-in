<?php declare(strict_types=1);

namespace App\Criticalmass\Router\ParameterResolver;

use App\Criticalmass\Router\Attribute\DefaultParameter;
use App\EntityInterface\RouteableInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ClassParameterResolver implements ParameterResolverInterface
{
    public function __construct(private readonly ParameterBagInterface $parameterBag)
    {

    }

    public function resolve(RouteableInterface $routeable, string $variableName): ?string
    {
        $reflectionClass = new \ReflectionClass($routeable);

        $classAttributes = $reflectionClass->getAttributes();

        foreach ($classAttributes as $classAttribute) {
            if ($classAttribute instanceof DefaultParameter) {
                if ($classAttribute->getRouteParameterName() !== $variableName) {
                    continue;
                }

                if ($this->parameterBag->has($classAttribute->getParameterName())) {
                    return $this->parameterBag->get($classAttribute->getParameterName());
                }
            }
        }

        return null;
    }
}