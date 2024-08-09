<?php declare(strict_types=1);

namespace App\Criticalmass\Router;

use App\Criticalmass\Router\DelegatedRouterManager\DelegatedRouterManagerInterface;
use App\Criticalmass\Router\ParameterResolver\ClassParameterResolver;
use App\Criticalmass\Router\ParameterResolver\PropertyParameterResolver;
use App\EntityInterface\RouteableInterface;
use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class ObjectRouter extends AbstractRouter implements ObjectRouterInterface
{
    public function __construct(
        RouterInterface $router,
        ClassParameterResolver $classParameterResolver,
        PropertyParameterResolver $propertyParameterResolver,
        private readonly DelegatedRouterManagerInterface $delegatedRouterManager,
    )
    {
        $this->delegatedRouterManager->setObjectRouter($this);

        parent::__construct($router, $classParameterResolver, $propertyParameterResolver);

    }

    public function generate(RouteableInterface $routeable, string $routeName = null, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        if (!$routeName) {
            $routeName = $this->getDefaultRouteName($routeable);
        }
        
        if ($delegatedRouter = $this->delegatedRouterManager->findDelegatedRouter($routeable)) {
            return $delegatedRouter->generate($routeable, $routeName, $parameters, $referenceType);
        }

        if ($routeName) {
            $parameterList = array_merge($this->generateParameterList($routeable, $routeName), $parameters);

            try {
                return $this->router->generate($routeName, $parameterList, $referenceType);
            } catch (InvalidParameterException $exception) {
                $delegatedRouter = $this->delegatedRouterManager->findDelegatedRouter($routeable);

                return $delegatedRouter->generate($routeable, $routeName, $parameters, $referenceType);
            }
        }
    }

    protected function getDefaultRouteName(RouteableInterface $routeable): ?string
    {
        $realFqcn = ClassUtils::getRealClass(get_class($routeable));
        $reflectionClass = new \ReflectionClass($realFqcn);

        $reflectionAttributes = $reflectionClass->getAttributes();

        foreach ($reflectionAttributes as $reflectionAttribute) {
            if ($reflectionAttribute->getName() === 'App\Criticalmass\Router\Attribute\DefaultRoute') {
                return $reflectionAttribute->getArguments()['name'];
            }
        }

        return null;
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
}
