<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Factory\QueryFactory;

use App\Criticalmass\DataQuery\RequestParameterList\RequestParameterList;

interface QueryFactoryInterface
{
    public function setEntityFqcn(string $entityFqcn): QueryFactoryInterface;

    public function createFromList(RequestParameterList $requestParameterList): array;
}
