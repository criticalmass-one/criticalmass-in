<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\FieldList\ParameterFieldList;

use App\Criticalmass\DataQuery\FieldList\AbstractField;

class ParameterField extends AbstractField
{
    /** @var string $parameterName */
    protected $parameterName;

    /** @var bool $requiresQueryable */
    protected $requiresQueryable = false;

    /** @var bool $requiresSortable */
    protected $requiresSortable = false;

    /** @var string $dateTimeFormat */
    protected $dateTimeFormat;

    /** @var string $dateTimePattern */
    protected $dateTimePattern;

    public function getParameterName(): string
    {
        return $this->parameterName;
    }

    public function setParameterName(string $parameterName): ParameterField
    {
        $this->parameterName = $parameterName;

        return $this;
    }

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
