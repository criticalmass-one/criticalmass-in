<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Property;

class EntityProperty
{
    /** @var string $propertyName */
    protected $propertyName;

    /** @var string $propertyType */
    protected $propertyType;

    /** @var string $dateTimeFormat */
    protected $dateTimeFormat;

    /** @var string $dateTimePattern */
    protected $dateTimePattern;

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

    public function setPropertyType(string $propertyType = null): EntityProperty
    {
        $this->propertyType = $propertyType;

        return $this;
    }

    public function getDateTimeFormat(): string
    {
        return $this->dateTimeFormat;
    }

    public function setDateTimeFormat(string $dateTimeFormat): EntityProperty
    {
        $this->dateTimeFormat = $dateTimeFormat;

        return $this;
    }

    public function getDateTimePattern(): string
    {
        return $this->dateTimePattern;
    }

    public function setDateTimePattern(string $dateTimePattern): EntityProperty
    {
        $this->dateTimePattern = $dateTimePattern;

        return $this;
    }
}
