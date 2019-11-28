<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\FieldList;

abstract class AbstractFieldList
{
    /** @var array $list */
    protected $list = [];

    protected function addToList(string $fieldName, AbstractField $field): self
    {
        if (!array_key_exists($fieldName, $this->list)) {
            $this->list[$fieldName] = [];
        }

        $this->list[$fieldName][] = $field;

        return $this;
    }

    public function getList(): array
    {
        return $this->list;
    }
}
