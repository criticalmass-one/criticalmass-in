<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Parameter;

interface PropertyTargetingParameterInterface extends ParameterInterface
{
    public function getPropertyName(): string;
}
