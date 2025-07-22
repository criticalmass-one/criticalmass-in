<?php declare(strict_types=1);

namespace App\Criticalmass\Router\Attribute;

#[\Attribute]
class DefaultParameter implements AttributeInterface
{
    public function __construct(
        private string $routeParameterName,
        private string $parameterName
    )
    {

    }

    public function getRouteParameterName(): string
    {
        return $this->routeParameterName;
    }

    public function getParameterName(): string
    {
        return $this->parameterName;
    }
}
