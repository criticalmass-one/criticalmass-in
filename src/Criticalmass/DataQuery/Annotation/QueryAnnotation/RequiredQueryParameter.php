<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Annotation\QueryAnnotation;

use App\Criticalmass\DataQuery\Annotation\AbstractAnnotation;

/**
 * @Annotation
 */
class RequiredQueryParameter extends AbstractAnnotation implements QueryAnnotationInterface
{
    /** @var string $parameterName */
    protected $parameterName;

    public function getParameterName(): string
    {
        return $this->parameterName;
    }
}
