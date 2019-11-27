<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\QueryFieldList;

use App\Criticalmass\DataQuery\Annotation\RequiredQueryParameter;
use App\Criticalmass\DataQuery\Exception\NotOneParameterForRequiredMethodException;
use App\Criticalmass\DataQuery\Exception\NoTypedParameterForRequiredMethodException;
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
        $this->queryFieldList = new QueryFieldList();
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
                if ($methodAnnotation instanceof RequiredQueryParameter) {

                    if ($reflectionMethod->getNumberOfParameters() !== 1) {
                        throw new NotOneParameterForRequiredMethodException($reflectionMethod->getName(), $this->queryFqcn);
                    }

                    $methodParameter = $reflectionMethod->getParameters()[0];
                    $reflectionType = $methodParameter->getType();

                    if (!$reflectionType) {
                        throw new NoTypedParameterForRequiredMethodException($reflectionMethod->getName(), $this->queryFqcn);
                    }

                    $entityField = new QueryField();
                    $entityField
                        ->setMethodName($reflectionMethod->getName())
                        ->setParameterName($methodAnnotation->getParameterName())
                        ->setType($reflectionType->getName());

                    $this->queryFieldList->addField($reflectionMethod->getName(), $entityField);
                }
            }
        }
    }
}
