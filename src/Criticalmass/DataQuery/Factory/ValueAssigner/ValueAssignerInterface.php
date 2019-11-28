<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Factory\ValueAssigner;

use App\Criticalmass\DataQuery\Parameter\ParameterInterface;
use App\Criticalmass\DataQuery\Property\ParameterProperty;
use App\Criticalmass\DataQuery\Property\QueryProperty;
use App\Criticalmass\DataQuery\Query\QueryInterface;
use App\Criticalmass\DataQuery\RequestParameterList\RequestParameterList;

interface ValueAssignerInterface
{
    public function assignQueryPropertyValue(RequestParameterList $requestParameterList, QueryInterface $query, QueryProperty $property): QueryInterface;

    public function assignParameterPropertyValue(RequestParameterList $requestParameterList, ParameterInterface $parameter, ParameterProperty $property): ParameterInterface;
}