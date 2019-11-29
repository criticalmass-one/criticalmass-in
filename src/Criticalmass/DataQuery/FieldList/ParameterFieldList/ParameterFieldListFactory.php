<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\FieldList\ParameterFieldList;

use App\Criticalmass\DataQuery\Annotation\AnnotationInterface;
use App\Criticalmass\DataQuery\Annotation\ParameterAnnotation\RequiredParameter;
use App\Criticalmass\DataQuery\Annotation\ParameterAnnotation\RequireSortableTargetProperty;
use App\Criticalmass\DataQuery\Exception\NotOneParameterForRequiredMethodException;
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

                    if ($propertyAnnotation instanceof RequiredParameter) {
                        $parameterField->setParameterName($propertyAnnotation->getParameterName());
                    }

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

                    if ($reflectionMethod->getNumberOfParameters() !== 1) {
                        throw new NotOneParameterForRequiredMethodException($reflectionMethod->getName(), $this->parameterFqcn);
                    }

                    $parameterField = new ParameterField();
                    $parameterField->setMethodName($reflectionMethod->getName());

                    $methodParameter = $reflectionMethod->getParameters()[0];
                    $reflectionType = $methodParameter->getType();

                    if ($reflectionType) {
                        $parameterField->setType($reflectionType->getName());
                    }

                    if ($methodAnnotation instanceof RequiredParameter) {
                        $parameterField->setParameterName($methodAnnotation->getParameterName());
                    }

                    if ($methodAnnotation instanceof RequireSortableTargetProperty) {
                        $parameterField->setRequiresQueryable(true);
                    }

                    $this->parameterFieldList->addField($reflectionMethod->getName(), $parameterField);
                }
            }
        }
    }
}
