<?php declare(strict_types=1);

namespace App\Criticalmass\Sharing\Annotation;

/**
 * @Annotation
 */
class RouteParameter extends AbstractAnnotation
{
    protected $name;

    public function getName(): ?string
    {
        return $this->name;
    }
}
