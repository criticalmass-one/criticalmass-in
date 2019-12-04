<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Annotation\ParameterAnnotation;

use App\Criticalmass\DataQuery\Annotation\AbstractAnnotation;

/**
 * @Annotation
 */
class RequiredParameter extends AbstractAnnotation implements ParameterAnnotationInterface
{
    /** @var string $parameterName */
    protected $parameterName;

    public function getParameterName(): string
    {
        return $this->parameterName;
    }
}
