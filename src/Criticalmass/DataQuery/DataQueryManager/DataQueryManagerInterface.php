<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\DataQueryManager;

use App\Criticalmass\DataQuery\RequestParameterList\RequestParameterList;

interface DataQueryManagerInterface
{
    public function query(RequestParameterList $requestParameterList, string $entityFqcn): array;
}