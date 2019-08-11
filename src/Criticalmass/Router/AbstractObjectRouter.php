<?php declare(strict_types=1);

namespace App\Criticalmass\Router;

use App\Criticalmass\Router\Annotation\DefaultRoute;
use App\Criticalmass\Router\ParameterResolver\ClassParameterResolver;
use App\Criticalmass\Router\ParameterResolver\PropertyParameterResolver;
use App\EntityInterface\RouteableInterface;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Routing\RouterInterface;

abstract class AbstractObjectRouter
{
    /** @var RouterInterface $router */
    protected $router;

    /** @var Reader $annotationReader */
    protected $annotationReader;

    /** @var ClassParameterResolver $classParameterResolver */
    protected $classParameterResolver;

    /** @var PropertyParameterResolver $propertyParameterResolver */
    protected $propertyParameterResolver;

    public function __construct(RouterInterface $router, Reader $annotationReader, ClassParameterResolver $classParameterResolver, PropertyParameterResolver $propertyParameterResolver)
    {
        $this->router = $router;
        $this->annotationReader = $annotationReader;

        $this->classParameterResolver = $classParameterResolver;
        $this->propertyParameterResolver = $propertyParameterResolver;
    }

    protected function getDefaultRouteName(RouteableInterface $routeable): ?string
    {
        /* It looks like Doctrine Annotation Reader cannot handle class annotations of Doctrine proxy objects so we do
         * not inject the $routeable itself but its classname */
        $classname = $this->getClassname($routeable);
        $fqcn = sprintf('App\\Entity\\%s', $classname);

        $reflectionClass = new \ReflectionClass($fqcn);

        $defaultRouteAnnotation = $this->annotationReader->getClassAnnotation($reflectionClass, DefaultRoute::class);

        if ($defaultRouteAnnotation) {
            return $defaultRouteAnnotation->getName();
        }

        return null;
    }

    public function getRouteParameter(RouteableInterface $routeable, string $variableName): ?string
    {
        return $this->classParameterResolver->resolve($routeable, $variableName) ?? $this->propertyParameterResolver->resolve($routeable, $variableName) ?? null;
    }

    protected function generateParameterList(RouteableInterface $routeable, string $routeName): array
    {
        $route = $this->router->getRouteCollection()->get($routeName);

        $compiledRoute = $route->compile();

        $variableList = $compiledRoute->getVariables();
        $parameterList = [];

        foreach ($variableList as $variableName) {
            $parameterList[$variableName] = $this->getRouteParameter($routeable, $variableName);
        }

        return $parameterList;
    }

    protected function getClassname(RouteableInterface $routeable): string
    {
        $classNameParts = explode('\\', get_class($routeable));
        $className = array_pop($classNameParts);

        return $className;
    }
}
