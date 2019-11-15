<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Factory\ValueAssigner;

use App\Criticalmass\DataQuery\Parameter\ParameterInterface;
use App\Criticalmass\DataQuery\Property\ParameterProperty;
use App\Criticalmass\DataQuery\Property\QueryProperty;
use App\Criticalmass\DataQuery\Query\QueryInterface;
use Symfony\Component\HttpFoundation\Request;

interface ValueAssignerInterface
{
    public function assignQueryPropertyValue(Request $request, QueryInterface $query, QueryProperty $property): QueryInterface;

    public function assignParameterPropertyValue(Request $request, ParameterInterface $parameter, ParameterProperty $property): ParameterInterface;
}