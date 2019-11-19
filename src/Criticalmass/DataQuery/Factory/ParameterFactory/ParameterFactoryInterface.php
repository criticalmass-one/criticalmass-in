<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Factory\ParameterFactory;

use Symfony\Component\HttpFoundation\Request;

interface ParameterFactoryInterface
{
    public function setEntityFqcn(string $entityFqcn): ParameterFactoryInterface;
    public function createFromRequest(Request $request): array;
}
