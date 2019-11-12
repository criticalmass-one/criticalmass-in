<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\AnnotationHandler;

interface AnnotationHandlerInterface
{
    public function hasEntityTypedPropertyOrMethodWithAnnotation(string $entityFqcn, string $annotationFqcn, string $propertyName, string $propertyType = null): bool;
}
