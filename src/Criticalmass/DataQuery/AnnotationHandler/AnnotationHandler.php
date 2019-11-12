<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\AnnotationHandler;

use App\Criticalmass\DataQuery\Annotation\RequiredQueryParameter;
use App\Criticalmass\DataQuery\QueryProperty\QueryProperty;
use Doctrine\Common\Annotations\Reader;

class AnnotationHandler implements AnnotationHandlerInterface
{
    /** @var Reader $annotationReader */
    protected $annotationReader;

    public function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    public function hasEntityTypedPropertyOrMethodWithAnnotation(string $entityFqcn, string $annotationFqcn, string $propertyName, string $propertyType = null): bool
    {
        $reflectionClass = new \ReflectionClass($entityFqcn);

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            if ($propertyName !== $reflectionProperty->getName()) {
                continue;
            }

            foreach ($this->annotationReader->getPropertyAnnotations($reflectionProperty) as $propertyAnnotation) {
                if ($propertyAnnotation instanceof $annotationFqcn) {
                    return true;
                }
            }
        }

        $expectedMethodName = sprintf('get%s', ucfirst($propertyName));

        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            if ($expectedMethodName !== $reflectionMethod->getName()) {
                continue;
            }

            foreach ($this->annotationReader->getMethodAnnotations($reflectionMethod) as $methodAnnotation) {
                if ($methodAnnotation instanceof $annotationFqcn) {
                    if ($propertyType && $propertyType === $reflectionMethod->getReturnType()->getName()) {
                        return true;
                    } elseif ($propertyType) {
                        return false;
                    }

                    return true;
                }
            }
        }

        return false;
    }

    public function listQueryRequiredMethods(string $queryFqcn): array
    {
        $requiredMethodList = [];

        $reflectionClass = new \ReflectionClass($queryFqcn);

        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            foreach ($this->annotationReader->getMethodAnnotations($reflectionMethod) as $propertyAnnotation) {
                if ($propertyAnnotation instanceof RequiredQueryParameter) {
                    $parameterType = $reflectionMethod->getParameters()[0]->getType()->getName();
                    
                    $queryProperty = new QueryProperty();
                    $queryProperty->setMethodName($reflectionMethod->getName())
                        ->setType($parameterType)
                        ->setParameterName($propertyAnnotation->getParameterName());

                    $requiredMethodList[] = $queryProperty;
                }
            }
        }
        
        return $requiredMethodList;
    }
}
