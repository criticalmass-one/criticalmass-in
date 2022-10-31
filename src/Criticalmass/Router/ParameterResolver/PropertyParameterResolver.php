<?php declare(strict_types=1);

namespace App\Criticalmass\Router\ParameterResolver;

use App\Criticalmass\Router\Annotation\AbstractAnnotation;
use App\Criticalmass\Router\Annotation\RouteParameter;
use App\Criticalmass\Router\DelegatedRouterManager\DelegatedRouterManagerInterface;
use App\EntityInterface\RouteableInterface;
use Doctrine\Common\Annotations\Reader;

class PropertyParameterResolver extends AbstractParameterResolver
{
    /** @var DelegatedRouterManagerInterface $delegatedRouterManager */
    protected $delegatedRouterManager;

    /** @var ClassParameterResolver $classParameterResolver */
    protected $classParameterResolver;

    public function __construct(Reader $annotationReader, DelegatedRouterManagerInterface $delegatedRouterManager, ClassParameterResolver $classParameterResolver)
    {
        $this->delegatedRouterManager = $delegatedRouterManager;
        $this->classParameterResolver = $classParameterResolver;

        parent::__construct($annotationReader);
    }

    public function resolve(RouteableInterface $routeable, string $variableName): ?string
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
                        if ($delegatedRouter = $this->delegatedRouterManager->findDelegatedRouter($value)) {
                            $value = $delegatedRouter->getRouteParameter($value, $variableName);
                        } else {
                            return $this->classParameterResolver->resolve($value, $variableName) ?? $this->resolve($value, $variableName) ?? null;
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
}