<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Property;

class QueryProperty
{
    /** @var string $propertyName */
    protected $propertyName;

    /** @var string $methodName */
    protected $methodName;

    /** @var string $parameterName */
    protected $parameterName;

    /** @var string $type */
    protected $type;

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    public function setPropertyName(string $propertyName): QueryProperty
    {
        $this->propertyName = $propertyName;

        return $this;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function setMethodName(string $methodName): QueryProperty
    {
        $this->methodName = $methodName;

        return $this;
    }

    public function getParameterName(): string
    {
        return $this->parameterName;
    }

    public function setParameterName(string $parameterName): QueryProperty
    {
        $this->parameterName = $parameterName;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): QueryProperty
    {
        $this->type = $type;

        return $this;
    }
}
