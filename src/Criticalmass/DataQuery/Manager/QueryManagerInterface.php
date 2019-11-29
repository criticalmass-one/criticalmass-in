<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Manager;

use App\Criticalmass\DataQuery\Query\QueryInterface;

interface QueryManagerInterface
{
    public function addQuery(QueryInterface $query): QueryManagerInterface;

    public function getQueryList(): array;
}
