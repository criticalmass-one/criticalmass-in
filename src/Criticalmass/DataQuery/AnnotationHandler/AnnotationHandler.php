<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\AnnotationHandler;

use App\Criticalmass\DataQuery\Annotation\AnnotationInterface;
use App\Criticalmass\DataQuery\Annotation\DefaultBooleanValue;
use App\Criticalmass\DataQuery\Annotation\RequiredEntityProperty;
use App\Criticalmass\DataQuery\Annotation\RequiredParameter;
use App\Criticalmass\DataQuery\Annotation\RequiredQueryParameter;
use App\Criticalmass\DataQuery\Annotation\RequireSortableTargetProperty;
use App\Criticalmass\DataQuery\Exception\MissingMethodParameterException;
use App\Criticalmass\DataQuery\Property\EntityBooleanValueProperty;
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

    public function getEntityPropertyOrMethodWithAnnotation(string $entityFqcn, string $annotationFqcn, string $propertyName): ?AnnotationInterface
    {
        $reflectionClass = new \ReflectionClass($entityFqcn);

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            if ($propertyName !== $reflectionProperty->getName()) {
                continue;
            }

            foreach ($this->annotationReader->getPropertyAnnotations($reflectionProperty) as $propertyAnnotation) {
                if ($propertyAnnotation instanceof $annotationFqcn) {
                    return $propertyAnnotation;
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
                    return $methodAnnotation;
                }
            }
        }

        return null;
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
                if ($propertyAnnotation instanceof RequiredParameter || $propertyAnnotation instanceof RequireSortableTargetProperty) {
                    $listKey = $reflectionMethod->getName();

                    if (!array_key_exists($listKey, $requiredMethodList)) {
                        $requiredMethodList[$listKey] = new ParameterProperty();
                        $requiredMethodList[$listKey]->setMethodName($reflectionMethod->getName());
                    }

                    if ($propertyAnnotation instanceof RequiredParameter) {
                        $firstParameter = $reflectionMethod->getParameters()[0];

                        if (!$firstParameter) {
                            throw new MissingMethodParameterException($reflectionMethod->getName(), $reflectionClass->getName());
                        }

                        $parameterType = $firstParameter->getType() ? $firstParameter->getType()->getName() : 'mixed';

                        $requiredMethodList[$listKey]
                            ->setType($parameterType)
                            ->setParameterName($propertyAnnotation->getParameterName());
                    }

                    if ($propertyAnnotation instanceof RequireSortableTargetProperty) {
                        $requiredMethodList[$listKey]->setRequiredSortableTargetEntity(true);
                    }
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

    public function listEntityDefaultValues(string $entityFqcn): array
    {
        $entityDefaultValueList = [];

        $reflectionClass = new \ReflectionClass($entityFqcn);

        /** @var \ReflectionProperty $reflectionProperty */
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            foreach ($this->annotationReader->getPropertyAnnotations($reflectionProperty) as $propertyAnnotation) {
                if ($propertyAnnotation instanceof DefaultBooleanValue) {
                    $propertyName = $propertyAnnotation->getAlias() ?? $reflectionProperty->getName();

                    $entityProperty = new EntityBooleanValueProperty();
                    $entityProperty
                        ->setValue($propertyAnnotation->getValue())
                        ->setPropertyName($propertyName);

                    $entityDefaultValueList[] = $entityProperty;
                }
            }
        }

        return $entityDefaultValueList;
    }

    public function hasEntityAnnotatedMethod(string $entityFqcn, string $methodName, string $annotationFqcn): bool
    {
        $reflectionClass = new \ReflectionClass($entityFqcn);

        if (!$reflectionClass->hasMethod($methodName)) {
            return false;
        }

        $reflectionMethod = $reflectionClass->getMethod($methodName);

        $annotationList = $this->annotationReader->getMethodAnnotations($reflectionMethod);

        foreach ($annotationList as $annotation) {
            if ($annotation instanceof $annotationFqcn) {
                return true;
            }
        }

        return false;
    }

    public function findChildAnnotationsForProperty(\ReflectionProperty $reflectionProperty, string $parentFqcn): array
    {
        $allPropertyAnnotations = $this->annotationReader->getPropertyAnnotations($reflectionProperty);
        $childAnnotations = [];

        foreach ($allPropertyAnnotations as $propertyAnnotation) {
            if ($propertyAnnotation instanceof $parentFqcn) {
                $childAnnotations[] = $propertyAnnotation;
            }
        }

        return $childAnnotations;
    }
}
