<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\ParameterFieldList;

use App\Criticalmass\DataQuery\Annotation\AnnotationInterface;
use App\Criticalmass\DataQuery\Annotation\RequireSortableTargetProperty;
use App\Criticalmass\DataQuery\Exception\NoTypedParameterForParameterMethodException;
use Doctrine\Common\Annotations\Reader as AnnotationReader;

class ParameterFieldListFactory implements ParameterFieldListFactoryInterface
{
    /** @var AnnotationReader $annotationReader */
    protected $annotationReader;

    /** @var string $parameterFqcn */
    protected $parameterFqcn;

    /** @var ParameterFieldList $parameterFieldList */
    protected $parameterFieldList;

    public function __construct(AnnotationReader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    public function createForFqcn(string $parameterFqcn): ParameterFieldList
    {
        $this->parameterFieldList = new ParameterFieldList();
        $this->parameterFqcn = $parameterFqcn;

        $this->addParameterPropertiesToList();
        $this->addParameterMethodsToList();

        return $this->parameterFieldList;
    }

    protected function addParameterPropertiesToList(): void
    {
        $reflectionClass = new \ReflectionClass($this->parameterFqcn);

        /** @var \ReflectionProperty $reflectionProperty */
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $propertyAnnotations = $this->annotationReader->getPropertyAnnotations($reflectionProperty);

            foreach ($propertyAnnotations as $propertyAnnotation) {
                if ($propertyAnnotation instanceof AnnotationInterface) {
                    $parameterField = new ParameterField();
                    $parameterField->setPropertyName($reflectionProperty->getName());

                    $this->parameterFieldList->addField($reflectionProperty->getName(), $parameterField);
                }
            }
        }
    }

    protected function addParameterMethodsToList(): void
    {
        $reflectionClass = new \ReflectionClass($this->parameterFqcn);

        /** @var \ReflectionMethod $reflectionMethod */
        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            $methodAnnotations = $this->annotationReader->getMethodAnnotations($reflectionMethod);

            foreach ($methodAnnotations as $methodAnnotation) {
                if ($methodAnnotation instanceof AnnotationInterface) {
                    /** @var \ReflectionType $returnType */
                    $returnType = $reflectionMethod->getReturnType();

                    if (!$returnType) {
                        throw new NoTypedParameterForParameterMethodException($reflectionMethod->getName(), $this->parameterFqcn);
                    }

                    $parameterField = new ParameterField();
                    $parameterField
                        ->setMethodName($reflectionMethod->getName())
                        ->setType($returnType->getName());

                    if ($methodAnnotation instanceof RequireSortableTargetProperty) {
                        $parameterField->setRequiresQueryable(true);
                    }

                    $this->parameterFieldList->addField($reflectionMethod->getName(), $parameterField);
                }
            }
        }
    }
}
