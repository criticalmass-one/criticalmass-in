<?php declare(strict_types=1);

namespace App\Criticalmass\OrderedEntities\Annotation;

/**
 * @Annotation
 */
class Boolean extends AbstractAnnotation
{
    /** @var bool $value */
    protected $value;

    public function getValue(): ?bool
    {
        return $this->value;
    }
}
