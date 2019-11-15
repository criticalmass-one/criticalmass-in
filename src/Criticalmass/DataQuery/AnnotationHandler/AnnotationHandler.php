<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\AnnotationHandler;

use App\Criticalmass\DataQuery\Annotation\RequiredEntityProperty;
use App\Criticalmass\DataQuery\Annotation\RequiredParameter;
use App\Criticalmass\DataQuery\Annotation\RequiredQueryParameter;
use App\Criticalmass\DataQuery\Annotation\RequireSortableTargetProperty;
use App\Criticalmass\DataQuery\Property\EntityProperty;
use App\Criticalmass\DataQuery\Property\ParameterProperty;
use App\Criticalmass\DataQuery\Property\QueryProperty;
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

    public function listParameterRequiredMethods(string $parameterFqcn): array
    {
        $requiredMethodList = [];

        $reflectionClass = new \ReflectionClass($parameterFqcn);

        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            foreach ($this->annotationReader->getMethodAnnotations($reflectionMethod) as $propertyAnnotation) {
                if ($propertyAnnotation instanceof RequiredParameter) {
                    $parameterType = $reflectionMethod->getParameters()[0]->getType()->getName();

                    $parameterProperty = new ParameterProperty();
                    $parameterProperty->setMethodName($reflectionMethod->getName())
                        ->setType($parameterType)
                        ->setParameterName($propertyAnnotation->getParameterName());

                    $requiredMethodList[$parameterProperty->getMethodName()] = $parameterProperty;
                }

                /** TODO null pointer checks here */
                if ($propertyAnnotation instanceof RequireSortableTargetProperty) {
                    $requiredMethodList[$reflectionMethod->getName()]->setRequiredSortableTargetEntity(true);
                }
            }
        }

        return $requiredMethodList;
    }

    public function listRequiredEntityProperties(string $queryFqcn): array
    {
        $requiredEntityPropertyList = [];

        $reflectionClass = new \ReflectionClass($queryFqcn);

        foreach ($this->annotationReader->getClassAnnotations($reflectionClass) as $classAnnotation) {
            if ($classAnnotation instanceof RequiredEntityProperty) {
                $entityProperty = new EntityProperty();
                $entityProperty
                    ->setPropertyName($classAnnotation->getPropertyName())
                    ->setPropertyType($classAnnotation->getPropertyType());

                $requiredEntityPropertyList[] = $entityProperty;
            }
        }

        return $requiredEntityPropertyList;
    }

    public function hasEntityAnnotatedMethod(string $entityFqcn, string $methodName, string $annotationFqcn): bool
    {
        $reflectionClass = new \ReflectionClass($entityFqcn);

        if (!$reflectionClass->hasMethod($methodName)) {
            return false;
        }

        $reflectionMethod = $reflectionClass->getMethod($methodName);

        $annotationList = $this->annotationReader->getMethodAnnotations($reflectionMethod);

        dump($annotationList);

        return false;
    }
}
