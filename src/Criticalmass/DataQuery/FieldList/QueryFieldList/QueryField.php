<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\FieldList\QueryFieldList;

use App\Criticalmass\DataQuery\FieldList\AbstractField;

class QueryField extends AbstractField
{
    /** @var string $parameterName */
    protected $parameterName;

    public function getParameterName(): string
    {
        return $this->parameterName;
    }

    public function setParameterName(string $parameterName): QueryField
    {
        $this->parameterName = $parameterName;

        return $this;
    }
}
