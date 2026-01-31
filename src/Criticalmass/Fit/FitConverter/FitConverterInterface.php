<?php declare(strict_types=1);

namespace App\Criticalmass\Fit\FitConverter;

interface FitConverterInterface
{
    public function convertToGpxString(string $fitFilePath): string;
}
