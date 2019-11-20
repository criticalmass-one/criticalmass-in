<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Annotation;

/**
 * @Annotation
 */
class RequiredQueryParameter extends AbstractAnnotation
{
    /** @var string $parameterName */
    protected $parameterName;

    public function getParameterName(): string
    {
        return $this->parameterName;
    }
}