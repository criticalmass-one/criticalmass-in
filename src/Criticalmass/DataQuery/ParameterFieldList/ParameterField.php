<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\ParameterFieldList;

class ParameterField
{
    /** @var bool $requiresQueryable */
    protected $requiresQueryable = false;

    /** @var bool $requiresSortable */
    protected $requiresSortable = false;

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

    public function requiresQueryable(): bool
    {
        return $this->requiresQueryable;
    }

    public function setRequiresQueryable(bool $requiresQueryable): ParameterField
    {
        $this->requiresQueryable = $requiresQueryable;

        return $this;
    }

    public function requiresSortable(): bool
    {
        return $this->requiresSortable;
    }

    public function setRequiresSortable(bool $requiresSortable): ParameterField
    {
        $this->requiresSortable = $requiresSortable;

        return $this;
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    public function setPropertyName(string $propertyName): ParameterField
    {
        $this->propertyName = $propertyName;

        return $this;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function setMethodName(string $methodName): ParameterField
    {
        $this->methodName = $methodName;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): ParameterField
    {
        $this->type = $type;

        return $this;
    }

    public function getDateTimeFormat(): string
    {
        return $this->dateTimeFormat;
    }

    public function setDateTimeFormat(string $dateTimeFormat): ParameterField
    {
        $this->dateTimeFormat = $dateTimeFormat;

        return $this;
    }

    public function getDateTimePattern(): string
    {
        return $this->dateTimePattern;
    }

    public function setDateTimePattern(string $dateTimePattern): ParameterField
    {
        $this->dateTimePattern = $dateTimePattern;

        return $this;
    }
}
