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

        foreach ($reflectionClass->getAttributes(DefaultParameter::class) as $classAttribute) {
            $defaultParameter = $classAttribute->newInstance();

            if ($defaultParameter->getRouteParameterName() !== $variableName) {
                continue;
            }

            if ($this->parameterBag->has($defaultParameter->getParameterName())) {
                return $this->parameterBag->get($defaultParameter->getParameterName());
            }
        }

        return null;
    }
}