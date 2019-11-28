<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Manager;

use App\Criticalmass\DataQuery\Parameter\ParameterInterface;

class ParameterManager implements ParameterManagerInterface
{
    /** @var array $parameterList */
    protected $parameterList = [];

    public function addParameter(ParameterInterface $parameter): ParameterManagerInterface
    {
        $this->parameterList[] = $parameter;

        return $this;
    }

    public function getParameterList(): array
    {
        return $this->parameterList;
    }
}
