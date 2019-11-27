<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\QueryFieldList;

interface QueryFieldListFactoryInterface
{
    public function createForFqcn(string $fqcn): QueryFieldList;
}
