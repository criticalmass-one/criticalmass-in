<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\EntityFieldList;

class EntityFieldListFactory
{
    public function createForFqcn(string $fqcn): EntityFieldList
    {
        $entityFieldList = new EntityFieldList();

        return $entityFieldList;
    }
}