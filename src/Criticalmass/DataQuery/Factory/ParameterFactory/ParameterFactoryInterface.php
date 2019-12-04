<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Factory\ParameterFactory;

use App\Criticalmass\DataQuery\RequestParameterList\RequestParameterList;

interface ParameterFactoryInterface
{
    public function setEntityFqcn(string $entityFqcn): ParameterFactoryInterface;

    public function createFromList(RequestParameterList $requestParameterList): array;
}
