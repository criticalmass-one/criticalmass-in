<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\FieldList\EntityFieldList;

use App\Criticalmass\DataQuery\FieldList\AbstractFieldList;

class EntityFieldList extends AbstractFieldList
{
    public function addField(string $fieldName, EntityField $entityProperty): EntityFieldList
    {
        $this->addToList($fieldName, $entityProperty);

        return $this;
    }
}
