<?php

namespace Caldera\GeoBundle\EntityInterface;

use Caldera\GeoBasic\Track\TrackInterface as BaseTrackInterface;

interface TrackInterface extends BaseTrackInterface
{
    public function setPreviewPolyline(string $previewPolyline = null): TrackInterface;
    public function getPreviewPolyline(): ?string;
}