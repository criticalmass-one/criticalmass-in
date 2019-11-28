<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Annotation;

/**
 * @Annotation
 */
class DefaultBooleanValue extends AbstractAnnotation
{
    /** @var string $alias */
    protected $alias;

    /** @var bool $value */
    protected $value;

    public function getAlias(): ?string
    {
        return $this->alias;
    }
    
    public function getValue(): bool
    {
        return $this->value;
    }
}