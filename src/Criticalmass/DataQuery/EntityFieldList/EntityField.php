<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\EntityFieldList;

class EntityField
{
    /** @var string $propertyName */
    protected $propertyName;

    /** @var string $methodName */
    protected $methodName;

    /** @var string $type */
    protected $type;

    /** @var string $dateTimeFormat */
    protected $dateTimeFormat;

    /** @var string $dateTimePattern */
    protected $dateTimePattern;

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    public function setPropertyName(string $propertyName): EntityField
    {
        $this->propertyName = $propertyName;

        return $this;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function setMethodName(string $methodName): EntityField
    {
        $this->methodName = $methodName;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): EntityField
    {
        $this->type = $type;

        return $this;
    }

    public function getDateTimeFormat(): string
    {
        return $this->dateTimeFormat;
    }

    public function setDateTimeFormat(string $dateTimeFormat): EntityField
    {
        $this->dateTimeFormat = $dateTimeFormat;

        return $this;
    }

    public function getDateTimePattern(): string
    {
        return $this->dateTimePattern;
    }

    public function setDateTimePattern(string $dateTimePattern): EntityField
    {
        $this->dateTimePattern = $dateTimePattern;

        return $this;
    }
}
