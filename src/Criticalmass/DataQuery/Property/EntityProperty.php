<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Property;

class EntityProperty
{
    /** @var string $propertyName */
    protected $propertyName;

    /** @var string $propertyType */
    protected $propertyType;

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    public function setPropertyName(string $propertyName): EntityProperty
    {
        $this->propertyName = $propertyName;
        
        return $this;
    }

    public function getPropertyType(): ?string
    {
        return $this->propertyType;
    }

    public function setPropertyType(string $propertyType): EntityProperty
    {
        $this->propertyType = $propertyType;

        return $this;
    }
}
