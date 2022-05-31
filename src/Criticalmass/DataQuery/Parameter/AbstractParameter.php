<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Parameter;

abstract class AbstractParameter implements ParameterInterface
{
    /** @var string $entityFqcn */
    protected $entityFqcn;

    public function setEntityFqcn(string $entityFqcn): ParameterInterface
    {
        $this->entityFqcn = $entityFqcn;

        return $this;
    }

    public function getEntityFqcn(): string
    {
        return $this->entityFqcn;
    }
}