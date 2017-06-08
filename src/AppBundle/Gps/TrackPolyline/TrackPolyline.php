<?php

namespace AppBundle\Gps\TrackPolyline;

use AppBundle\Entity\Track;
use AppBundle\Gps\GpxReader\TrackReader;
use Exception;
use PointReduction\Algorithms\RadialDistance;
use PointReduction\Common\Point;

class TrackPolyline
{
    /** @var Track $track */
    protected $track = null;

    protected $polyline = null;
    protected $xmlRootNode = null;
    protected $trackReader = null;
    protected $gapWidth = null;

    public function __construct(TrackReader $trackReader, $gapWidth)
    {
        $this->trackReader = $trackReader;
        $this->gapWidth = $gapWidth;
    }

    public function loadTrack(Track $track): TrackPolyline
    {
        $this->track = $track;

        $fileLoaded = $this->trackReader->loadTrack($this->track);

        if (!$fileLoaded) {
            throw new Exception('Could not load gpx file.');
        }

        $this->xmlRootNode = $this->trackReader->getRootNode();

        return $this;
    }

    public function getPolyline(): ?string
    {
        return $this->polyline;
    }

    public function generatePolyline(): TrackPolyline
    {
        $list = $this->trackReader->slicePublicCoords();

        $polyline = \Polyline::Encode($list);

        $this->polyline = $polyline;

        return $this;
    }

    public function generatePreviewPolyline(): TrackPolyline
    {
        $list = array_values($this->trackReader->slicePublicCoords());

        $tolerance = 0.0025;
        $reducer = new RadialDistance($list);

        $reducedPointList = $reducer->reduce($tolerance);
        $reducedList = [];

        /** @var Point $point */
        foreach ($reducedPointList as $point) {
            $reducedList[] = [$point->x, $point->y];
        }

        $this->polyline = \Polyline::Encode($reducedList);

        return $this;
    }
} 