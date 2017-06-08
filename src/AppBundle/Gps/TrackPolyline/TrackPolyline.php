<?php

namespace AppBundle\Gps\TrackPolyline;

use AppBundle\Entity\Track;
use AppBundle\Gps\GpxReader\TrackReader;
use PointReduction\Algorithms\RadialDistance;

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

        $this->trackReader->loadTrack($this->track);

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
        $list = $this->trackReader->slicePublicCoords();

        $tolerance = 0.0025;
        $reducer = new RadialDistance($list);
        $reducedList = $reducer->reduce($tolerance);

        $polyline = \Polyline::Encode($reducedList);

        $this->polyline = $polyline;

        return $this;
    }
} 