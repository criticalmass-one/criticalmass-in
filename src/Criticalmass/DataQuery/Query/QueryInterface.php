<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

interface QueryInterface
{
    public function isOverridenBy(): array;
}
