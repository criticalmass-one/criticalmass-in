<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\AnnotationHandler;

interface AnnotationHandlerInterface
{
    public function hasEntityPropertyOrMethodWithAnnotation(string $entityFqcn, string $propertyName, string $annotationFqcn): bool;
}
