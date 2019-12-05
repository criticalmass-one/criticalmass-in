<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

abstract class AbstractQuery implements QueryInterface
{
    public function isOverridenBy(): array
    {
        return [];
    }
}
