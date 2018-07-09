<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Router\Annotation;

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
