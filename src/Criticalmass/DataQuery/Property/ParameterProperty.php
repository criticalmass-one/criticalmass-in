<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Property;

class ParameterProperty
{
    /** @var string $propertyName */
    protected $propertyName;

    /** @var string $methodName */
    protected $methodName;

    /** @var string $parameterName */
    protected $parameterName;

    /** @var string $type */
    protected $type;

    /** @var bool $requiredSortableTargetEntity */
    protected $requiredSortableTargetEntity = false;

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    public function setPropertyName(string $propertyName): ParameterProperty
    {
        $this->propertyName = $propertyName;

        return $this;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function setMethodName(string $methodName): ParameterProperty
    {
        $this->methodName = $methodName;

        return $this;
    }

    public function getParameterName(): string
    {
        return $this->parameterName;
    }

    public function setParameterName(string $parameterName): ParameterProperty
    {
        $this->parameterName = $parameterName;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): ParameterProperty
    {
        $this->type = $type;

        return $this;
    }

    public function hasRequiredSortableTargetEntity(): bool
    {
        return $this->requiredSortableTargetEntity;
    }

    public function setRequiredSortableTargetEntity(bool $requiredSortableTargetEntity): ParameterProperty
    {
        $this->requiredSortableTargetEntity = $requiredSortableTargetEntity;

        return $this;
    }
}
