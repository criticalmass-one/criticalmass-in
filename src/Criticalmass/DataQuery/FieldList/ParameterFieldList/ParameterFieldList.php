<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\FieldList\ParameterFieldList;

use App\Criticalmass\DataQuery\FieldList\AbstractFieldList;

class ParameterFieldList extends AbstractFieldList
{
    public function addField(string $fieldName, ParameterField $parameterField): ParameterFieldList
    {
        $this->addToList($fieldName, $parameterField);

        return $this;
    }
}
