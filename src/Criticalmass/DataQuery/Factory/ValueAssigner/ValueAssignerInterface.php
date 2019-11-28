<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Factory\ValueAssigner;

use App\Criticalmass\DataQuery\FieldList\ParameterFieldList\ParameterField;
use App\Criticalmass\DataQuery\FieldList\QueryFieldList\QueryField;
use App\Criticalmass\DataQuery\Parameter\ParameterInterface;
use App\Criticalmass\DataQuery\Query\QueryInterface;
use App\Criticalmass\DataQuery\RequestParameterList\RequestParameterList;

interface ValueAssignerInterface
{
    public function assignQueryPropertyValue(RequestParameterList $requestParameterList, QueryInterface $query, QueryField $queryField): QueryInterface;

    public function assignParameterPropertyValue(RequestParameterList $requestParameterList, ParameterInterface $parameter, ParameterField $parameterField): ParameterInterface;
}