<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Finder;

interface FinderInterface
{
    public function executeQuery(array $queryList, array $parameterList): array;
}
