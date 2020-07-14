<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\FieldList\QueryFieldList;

use App\Criticalmass\DataQuery\FieldList\AbstractFieldList;

class QueryFieldList extends AbstractFieldList
{
    public function addField(string $fieldName, QueryField $queryField): QueryFieldList
    {
        $this->addToList($fieldName, $queryField);

        return $this;
    }
}
