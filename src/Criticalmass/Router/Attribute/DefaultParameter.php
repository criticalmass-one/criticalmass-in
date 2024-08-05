<?php declare(strict_types=1);

namespace App\Criticalmass\Router\Annotation;

/**
 * @Annotation
 */
class DefaultParameter extends AbstractAnnotation
{
    /** @var string $routeParameterName */
    protected $routeParameterName;

    /** @var string $parameterName */
    protected $parameterName;

    public function getRouteParameterName(): string
    {
        return $this->routeParameterName;
    }

    public function getParameterName(): string
    {
        return $this->parameterName;
    }
}
