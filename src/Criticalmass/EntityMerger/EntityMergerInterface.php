<?php declare(strict_types=1);

namespace App\Criticalmass\EntityMerger;

interface EntityMergerInterface
{
    public function merge(object $source, object $destination): object;
}
