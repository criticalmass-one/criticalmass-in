<?php declare(strict_types=1);

namespace App\Criticalmass\Sharing\Annotation;

/**
 * @Annotation
 */
class Title extends AbstractAnnotation
{
    protected $title;

    public function getTitle(): ?string
    {
        return $this->title;
    }
}
