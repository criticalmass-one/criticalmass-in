<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\EntityFieldList;

use App\Criticalmass\DataQuery\Annotation\AnnotationInterface;
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
                    $entityField = new EntityField();
                    $entityField->setMethodName($reflectionMethod->getName());

                    $this->entityFieldList->addField($reflectionMethod->getName(), $entityField);
                }
            }
        }
    }
}
