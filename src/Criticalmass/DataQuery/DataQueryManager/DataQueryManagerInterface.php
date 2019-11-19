<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\DataQueryManager;

use Symfony\Component\HttpFoundation\Request;

interface DataQueryManagerInterface
{
    public function queryForRequest(Request $request, string $entityFqcn): array;
}