<?php declare(strict_types=1);

namespace App\Criticalmass\Cycles\Analyzer;

class ComparisonResultInterface
{
    public const EQUAL = 0;
    public const NO_RIDE = 1;
    public const LOCATION_MISMATCH = 2;
    public const DATETIME_MISMATCH = 4;
}
