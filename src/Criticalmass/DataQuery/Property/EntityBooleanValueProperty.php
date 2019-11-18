<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Property;

class EntityBooleanValueProperty extends EntityProperty
{
    /** @var string $propertyType */
    protected $propertyType = 'bool';

    /** @var bool $value */
    protected $value = false;

    public function setValue(bool $value): EntityBooleanValueProperty
    {
        $this->value = $value;

        return $this;
    }

    public function getValue(): bool
    {
        return $this->value;
    }
}
