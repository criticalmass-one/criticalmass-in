<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\EntityFieldList;

interface EntityFieldListFactoryInterface
{
    public function createForFqcn(string $fqcn): EntityFieldList;
}
