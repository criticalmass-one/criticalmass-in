<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\FinderFactory;

use App\Criticalmass\DataQuery\Finder\FinderInterface;

interface FinderFactoryInterface
{
    public function createFinderForFqcn(string $fqcn): FinderInterface;
}
