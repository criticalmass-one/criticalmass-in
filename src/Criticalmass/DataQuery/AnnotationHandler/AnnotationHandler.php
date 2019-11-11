<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\AnnotationHandler;

use Doctrine\Common\Annotations\Reader;

class AnnotationHandler implements AnnotationHandlerInterface
{
    /** @var Reader $annotationReader */
    protected $annotationReader;

    /** @var string $entityFqcn */
    protected $entityFqcn;

    public function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    public function hasEntityPropertyOrMethodWithAnnotation(string $entityFqcn, string $propertyName, string $annotationFqcn): bool
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

        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            $expectedMethodName = sprintf('get%s', ucfirst($propertyName));

            if ($expectedMethodName !== $reflectionMethod->getName()) {
                continue;
            }

            foreach ($this->annotationReader->getMethodAnnotations($reflectionMethod) as $methodAnnotation) {
                if ($methodAnnotation instanceof $annotationFqcn) {
                    return true;
                }
            }
        }

        return false;
    }
}
