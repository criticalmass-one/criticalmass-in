<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\EntityFieldList;

class EntityFieldList
{
    /** @var array $list */
    protected $list = [];

    public function addField(string $fieldName, EntityField $entityProperty): EntityFieldList
    {
        if (!array_key_exists($fieldName, $this->list)) {
            $this->list[$fieldName] = [];
        }

        $this->list[$fieldName][] = $entityProperty;

        return $this;
    }

    public function getList(): array
    {
        return $this->list;
    }
}
