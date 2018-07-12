<?php declare(strict_types=1);

namespace App\Criticalmass\Sharing\Annotation;

/**
 * @Annotation
 */
class Route extends AbstractAnnotation
{
    protected $route;

    public function getRoute(): ?string
    {
        return $this->route;
    }
}
