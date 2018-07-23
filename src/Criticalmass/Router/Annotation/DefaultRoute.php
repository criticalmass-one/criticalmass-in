<?php declare(strict_types=1);

namespace App\Criticalmass\Router\Annotation;

/**
 * @Annotation
 */
class DefaultRoute extends AbstractAnnotation
{
    protected $name;

    public function getName(): ?string
    {
        return $this->name;
    }
}
