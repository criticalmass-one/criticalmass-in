<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\QueryFieldList;

use App\Criticalmass\DataQuery\Annotation\AnnotationInterface;
use App\Criticalmass\DataQuery\Annotation\DateTimeQueryable;
use App\Criticalmass\DataQuery\Annotation\Queryable;
use App\Criticalmass\DataQuery\Annotation\RequiredQueryParameter;
use App\Criticalmass\DataQuery\Annotation\Sortable;
use App\Criticalmass\DataQuery\Exception\NoReturnTypeForEntityMethodException;
use Doctrine\Common\Annotations\Reader as AnnotationReader;

class QueryFieldListFactory implements QueryFieldListFactoryInterface
{
    /** @var AnnotationReader $annotationReader */
    protected $annotationReader;

    /** @var string $queryFqcn */
    protected $queryFqcn;

    /** @var QueryFieldList $queryFieldList */
    protected $queryFieldList;

    public function __construct(AnnotationReader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    public function createForFqcn(string $queryFqcn): QueryFieldList
    {
        $this->entityFieldList = new QueryFieldList();
        $this->queryFqcn = $queryFqcn;

        $this->addQueryPropertiesToList();
        $this->addQueryMethodsToList();

        return $this->queryFieldList;
    }

    protected function addQueryPropertiesToList(): void
    {
        $reflectionClass = new \ReflectionClass($this->queryFqcn);

        /** @var \ReflectionProperty $reflectionProperty */
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $propertyAnnotations = $this->annotationReader->getPropertyAnnotations($reflectionProperty);

            foreach ($propertyAnnotations as $propertyAnnotation) {
                if ($propertyAnnotation instanceof RequiredQueryParameter) {
                    $queryField = new QueryField();
                    $queryField
                        ->setPropertyName($reflectionProperty->getName())
                        ->setParameterName($propertyAnnotation->getParameterName());
                    // TODO: Get property type here for php 7.4

                    $this->queryFieldList->addField($reflectionProperty->getName(), $queryField);
                }
            }
        }
    }

    protected function addQueryMethodsToList(): void
    {
        $reflectionClass = new \ReflectionClass($this->queryFqcn);

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

                    if ($methodAnnotation instanceof Sortable) {
                        $entityField->setSortable(true);
                    }

                    if ($methodAnnotation instanceof Queryable) {
                        $entityField->setQueryable(true);
                    }

                    if ($methodAnnotation instanceof DateTimeQueryable) {
                        $entityField
                            ->setDateTimePattern($methodAnnotation->getPattern())
                            ->setDateTimeFormat($methodAnnotation->getFormat());
                    }

                    $this->entityFieldList->addField($reflectionMethod->getName(), $entityField);
                }
            }
        }
    }
}
