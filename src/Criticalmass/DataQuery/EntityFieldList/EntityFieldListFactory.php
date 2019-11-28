<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\EntityFieldList;

use App\Criticalmass\DataQuery\Annotation\AnnotationInterface;
use App\Criticalmass\DataQuery\Annotation\DateTimeQueryable;
use App\Criticalmass\DataQuery\Annotation\DefaultBooleanValue;
use App\Criticalmass\DataQuery\Annotation\Queryable;
use App\Criticalmass\DataQuery\Annotation\Sortable;
use App\Criticalmass\DataQuery\Exception\NoReturnTypeForEntityMethodException;
use Doctrine\Common\Annotations\Reader as AnnotationReader;

class EntityFieldListFactory implements EntityFieldListFactoryInterface
{
    /** @var AnnotationReader $annotationReader */
    protected $annotationReader;

    /** @var string $entityFqcn */
    protected $entityFqcn;

    /** @var EntityFieldList $entityFieldList */
    protected $entityFieldList;

    public function __construct(AnnotationReader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    public function createForFqcn(string $entityFqcn): EntityFieldList
    {
        $this->entityFieldList = new EntityFieldList();
        $this->entityFqcn = $entityFqcn;

        $this->addEntityPropertiesToList();
        $this->addEntityMethodsToList();

        return $this->entityFieldList;
    }

    protected function addEntityPropertiesToList(): void
    {
        $reflectionClass = new \ReflectionClass($this->entityFqcn);

        /** @var \ReflectionProperty $reflectionProperty */
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $propertyAnnotations = $this->annotationReader->getPropertyAnnotations($reflectionProperty);

            foreach ($propertyAnnotations as $propertyAnnotation) {
                if ($propertyAnnotation instanceof AnnotationInterface) {
                    $entityField = new EntityField();
                    $entityField->setPropertyName($reflectionProperty->getName());

                    $entityField = $this->processAnnotations($propertyAnnotation, $entityField);

                    $this->entityFieldList->addField($reflectionProperty->getName(), $entityField);
                }
            }
        }
    }

    protected function addEntityMethodsToList(): void
    {
        $reflectionClass = new \ReflectionClass($this->entityFqcn);

        /** @var \ReflectionMethod $reflectionMethod */
        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            $methodAnnotations = $this->annotationReader->getMethodAnnotations($reflectionMethod);

            foreach ($methodAnnotations as $methodAnnotation) {
                if ($methodAnnotation instanceof AnnotationInterface) {
                    /** @var \ReflectionType $returnType */
                    $returnType = $reflectionMethod->getReturnType();

                    if (!$returnType) {
                        throw new NoReturnTypeForEntityMethodException($reflectionMethod->getName(), $this->entityFqcn);
                    }

                    $entityField = new EntityField();
                    $entityField
                        ->setMethodName($reflectionMethod->getName())
                        ->setType($returnType->getName());

                    $entityField = $this->processAnnotations($methodAnnotation, $entityField);

                    $this->entityFieldList->addField($reflectionMethod->getName(), $entityField);
                }
            }
        }
    }

    protected function processAnnotations(AnnotationInterface $annotation, EntityField $entityField): EntityField
    {
        if ($annotation instanceof Sortable) {
            $entityField->setSortable(true);
        }

        if ($annotation instanceof Queryable) {
            $entityField->setQueryable(true);
        }

        if ($annotation instanceof DateTimeQueryable) {
            $entityField
                ->setDateTimePattern($annotation->getPattern())
                ->setDateTimeFormat($annotation->getFormat());
        }

        if ($annotation instanceof DefaultBooleanValue) {
            $entityField
                ->setDefaultQueryBool(true)
                ->setDefaultQueryBoolValue($annotation->getValue());
        }

        return $entityField;
    }
}
