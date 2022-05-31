<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Manager;

use App\Criticalmass\DataQuery\Parameter\ParameterInterface;

interface ParameterManagerInterface
{
    public function addParameter(ParameterInterface $parameter): ParameterManagerInterface;

    public function getParameterList(): array;
}
