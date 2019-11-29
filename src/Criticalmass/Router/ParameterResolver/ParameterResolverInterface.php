<?php declare(strict_types=1);

namespace App\Criticalmass\Router\ParameterResolver;

use App\EntityInterface\RouteableInterface;

interface ParameterResolverInterface
{
    public function resolve(RouteableInterface $routeable, string $variableName): ?string;
}