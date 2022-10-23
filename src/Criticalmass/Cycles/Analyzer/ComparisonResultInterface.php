<?php declare(strict_types=1);

namespace App\Criticalmass\Cycles\Analyzer;

class ComparisonResultInterface
{
    final public const EQUAL = 0;
    final public const NO_RIDE = 1;
    final public const LOCATION_MISMATCH = 2;
    final public const DATETIME_MISMATCH = 4;
}
