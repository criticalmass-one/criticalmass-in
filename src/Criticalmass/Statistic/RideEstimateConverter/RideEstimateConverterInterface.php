<?php declare(strict_types=1);

namespace App\Criticalmass\Statistic\RideEstimateConverter;

use App\Entity\Track;

interface RideEstimateConverterInterface
{
    public function addEstimateFromTrack(Track $track, bool $flush = true): RideEstimateConverterInterface;
}
