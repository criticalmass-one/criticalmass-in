<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\TimeShifter;

interface GpxTimeShifterInterface extends TimeShifterInterface
{
    public function loadGpxFile(string $filename): GpxTimeShifterInterface;
}