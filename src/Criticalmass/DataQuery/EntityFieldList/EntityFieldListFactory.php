<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\EntityFieldList;

use App\Criticalmass\DataQuery\Annotation\AnnotationInterface;
use Doctrine\Common\Annotations\Reader as AnnotationReader;

class EntityFieldListFactory implements EntityFieldListFactoryInterface
{
    /** @var AnnotationReader $annotationReader */
    protected $annotationReader;

    public function __construct(AnnotationReader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    public function createForFqcn(string $fqcn): EntityFieldList
    {
        $entityFieldList = new EntityFieldList();

        $reflectionClass = new \ReflectionClass($fqcn);

        /** @var \ReflectionProperty $reflectionProperty */
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $propertyAnnotations = $this->annotationReader->getPropertyAnnotations($reflectionProperty);

            foreach ($propertyAnnotations as $propertyAnnotation) {
                if ($propertyAnnotation instanceof AnnotationInterface) {
                    $entityField = new EntityField();
                    $entityField->setPropertyName($reflectionProperty->getName());

                    $entityFieldList->addField($reflectionProperty->getName(), $entityField);
                }
            }
        }

        /** @var \ReflectionMethod $reflectionMethod */
        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            $methodAnnotations = $this->annotationReader->getMethodAnnotations($reflectionMethod);

            foreach ($methodAnnotations as $methodAnnotation) {
                if ($methodAnnotation instanceof AnnotationInterface) {
                    $entityField = new EntityField();
                    $entityField->setMethodName($reflectionMethod->getName());

                    $entityFieldList->addField($reflectionMethod->getName(), $entityField);
                }
            }
        }

        return $entityFieldList;
    }

}
