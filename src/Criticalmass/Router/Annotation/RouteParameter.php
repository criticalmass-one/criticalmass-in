<?php declare(strict_types=1);

namespace App\Criticalmass\Router\Annotation;

/**
 * @Annotation
 */
class RouteParameter extends AbstractAnnotation
{
    protected $name;

    protected $dateFormat;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getDateFormat(): ?string
    {
        return $this->dateFormat;
    }
}
