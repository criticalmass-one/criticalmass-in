<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\EntityInterface;

use Caldera\GeoBasic\Track\TrackInterface as BaseTrackInterface;

/** @deprecated  */
interface TrackInterface extends BaseTrackInterface
{
    public function setReducedPolyline(string $reducedPolyline = null): TrackInterface;
    public function getReducedPolyline(): ?string;
}
