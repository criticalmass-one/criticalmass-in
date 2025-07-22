<?php declare(strict_types=1);

namespace App\Criticalmass\Router;


use App\Criticalmass\Router\ParameterResolver\ClassParameterResolver;
use App\Criticalmass\Router\ParameterResolver\PropertyParameterResolver;
use App\EntityInterface\RouteableInterface;
use Symfony\Component\Routing\RouterInterface;

abstract class AbstractRouter
{
    public function __construct(
        protected readonly RouterInterface $router,
        protected readonly ClassParameterResolver $classParameterResolver,
        protected readonly PropertyParameterResolver $propertyParameterResolver,
    )
    {

    }

    public function getRouteParameter(RouteableInterface $routeable, string $variableName): ?string
    {
        return $this->classParameterResolver->resolve($routeable, $variableName)
            ?? $this->propertyParameterResolver->resolve($routeable, $variableName)
            ?? null
            ;
    }

    protected function generateParameterList(RouteableInterface $routeable, string $routeName): array
    {
        $route = $this->router->getRouteCollection()->get($routeName);

        if (!$route) {
            throw new \RuntimeException(sprintf('Route %s not found', $routeName));
        }

        $compiledRoute = $route->compile();

        $variableList = $compiledRoute->getVariables();
        $parameterList = [];

        foreach ($variableList as $variableName) {
            $parameterList[$variableName] = $this->getRouteParameter($routeable, $variableName);
        }

        $parameterList = $this->setupDefaultParameterValues($routeName, $parameterList);

        return $parameterList;
    }


    protected function setupDefaultParameterValues(string $routeName, array $parameters = []): array
    {
        $defaultsList = $this->router->getRouteCollection()->get($routeName)->getDefaults();

        foreach ($defaultsList as $parameterName => $parameterDefaultValue) {
            if (!array_key_exists($parameterName, $parameters) || empty($parameters[$parameterName])) {
                $parameters[$parameterName] = $parameterDefaultValue;
            }
        }

        return $parameters;
    }
}
