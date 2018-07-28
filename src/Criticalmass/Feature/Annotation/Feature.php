<?php declare(strict_types=1);

namespace App\Criticalmass\Feature\Annotation;

/**
 * @Annotation
 */
class Feature extends AbstractAnnotation
{
    protected $name;

    public function getName(): ?string
    {
        return $this->name;
    }
}
