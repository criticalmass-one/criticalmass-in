<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Annotation;

/**
 * @Annotation
 */
class RequiredEntityProperty extends AbstractAnnotation
{
    /** @var string $propertyName */
    protected $propertyName;

    /** @var string $propertyType */
    protected $propertyType;

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    public function getPropertyType(): ?string
    {
        return $this->propertyType;
    }
}
