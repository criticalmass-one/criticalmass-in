<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\ParameterFieldList;

interface ParameterFieldListFactoryInterface
{
    public function createForFqcn(string $fqcn): ParameterFieldList;
}
