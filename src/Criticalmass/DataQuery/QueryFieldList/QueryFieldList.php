<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\QueryFieldList;

class QueryFieldList
{
    /** @var array $list */
    protected $list = [];

    public function addField(string $fieldName, QueryField $queryField): QueryFieldList
    {
        if (!array_key_exists($fieldName, $this->list)) {
            $this->list[$fieldName] = [];
        }

        $this->list[$fieldName][] = $queryField;

        return $this;
    }
}
