<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Sharing\Annotation;

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
