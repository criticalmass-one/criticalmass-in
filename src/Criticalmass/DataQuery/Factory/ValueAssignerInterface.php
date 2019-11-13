<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Factory;

use App\Criticalmass\DataQuery\Query\QueryInterface;
use App\Criticalmass\DataQuery\QueryProperty\QueryProperty;
use Symfony\Component\HttpFoundation\Request;

interface ValueAssignerInterface
{
    public function assignPropertyValue(Request $request, QueryInterface $query, QueryProperty $property): QueryInterface;
}