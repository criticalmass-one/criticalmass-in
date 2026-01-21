<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\EntityInterface;

use App\Geo\Track\TrackInterface as BaseTrackInterface;

interface TrackInterface extends BaseTrackInterface
{
    public function setReducedPolyline(string $reducedPolyline = null): TrackInterface;
    public function getReducedPolyline(): ?string;
}
