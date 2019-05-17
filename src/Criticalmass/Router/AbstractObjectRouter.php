<?php declare(strict_types=1);

namespace App\Criticalmass\Router;

use App\Criticalmass\Router\Annotation\AbstractAnnotation;
use App\Criticalmass\Router\Annotation\DefaultRoute;
use App\Criticalmass\Router\Annotation\RouteParameter;
use App\Criticalmass\Router\DelegatedRouter\DelegatedRouterInterface;
use App\EntityInterface\RouteableInterface;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Routing\RouterInterface;

abstract class AbstractObjectRouter
{
    /** @var RouterInterface $router */
    protected $router;

    /** @var Reader $annotationReader */
    protected $annotationReader;

    /** @var array $delegatedRouterList */
    protected $delegatedRouterList = [];

    public function __construct(RouterInterface $router, Reader $annotationReader)
    {
        $this->router = $router;
        $this->annotationReader = $annotationReader;
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
        $reflectionClass = new \ReflectionClass($routeable);
        $properties = $reflectionClass->getProperties();

        foreach ($properties as $key => $property) {
            $parameterAnnotations = $this->annotationReader->getPropertyAnnotations($property);

            /** @var AbstractAnnotation $parameterAnnotation */
            foreach ($parameterAnnotations as $parameterAnnotation) {
                if ($parameterAnnotation instanceof RouteParameter) {
                    if ($parameterAnnotation->getName() !== $variableName) {
                        continue;
                    }

                    $getMethodName = sprintf('get%s', ucfirst($property->getName()));

                    if (!$reflectionClass->hasMethod($getMethodName)) {
                        continue;
                    }

                    $value = $routeable->$getMethodName();

                    if (is_object($value) && $value instanceof RouteableInterface) {
                        if ($delegatedRouter = $this->findDelegatedRouter($value)) {
                            $value = $delegatedRouter->getRouteParameter($value, $variableName);
                        } else {
                            $value = $this->getRouteParameter($value, $variableName);
                        }
                    }

                    if (is_object($value) && $value instanceof \DateTime) {
                        $value = $value->format($parameterAnnotation->getDateFormat());
                    }

                    return (string) $value;
                }
            }
        }

        return null;
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

    protected function findDelegatedRouter(RouteableInterface $routeable): ?DelegatedRouterInterface
    {
        /** @var DelegatedRouterInterface $delegatedRouter */
        foreach ($this->delegatedRouterList as $delegatedRouter) {
            if ($delegatedRouter->supports($routeable)) {
                return $delegatedRouter;
            }
        }

        return null;
    }
}
