<?php declare(strict_types=1);

namespace App\Criticalmass\Fit\FitParser;

interface FitParserInterface
{
    public function parse(string $filePath): FitData;
}
