<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\FieldList\ParameterFieldList;

class ParameterFieldList
{
    /** @var array $list */
    protected $list = [];

    public function addField(string $fieldName, ParameterField $parameterField): ParameterFieldList
    {
        if (!array_key_exists($fieldName, $this->list)) {
            $this->list[$fieldName] = [];
        }

        $this->list[$fieldName][] = $parameterField;

        return $this;
    }

    public function getList(): array
    {
        return $this->list;
    }
}
