<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\AnnotationHandler;

interface AnnotationHandlerInterface
{
    public function hasEntityTypedPropertyOrMethodWithAnnotation(string $entityFqcn, string $annotationFqcn, string $propertyName, string $propertyType = null): bool;

    public function listQueryRequiredMethods(string $queryFqcn): array;

    public function listParameterRequiredMethods(string $parameterFqcn): array;

    public function listRequiredEntityProperties(string $queryFqcn): array;

    public function listEntityDefaultValues(string $entityFqcn): array;

    public function hasEntityAnnotatedMethod(string $entityFqcn, string $propertyName, string $annotationFqcn): bool;
}
