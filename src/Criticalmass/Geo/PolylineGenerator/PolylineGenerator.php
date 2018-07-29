<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\PolylineGenerator;

use Caldera\GeoBundle\EntityInterface\TrackInterface;
use Caldera\GeoBundle\GpxReader\TrackReader;
use PointReduction\Algorithms\RadialDistance;
use PointReduction\Common\Point;

class PolylineGenerator
{
    /** @var TrackInterface $track */
    protected $track;

    /** @var TrackReader $trackReader */
    protected $trackReader;

    public function __construct(TrackReader $trackReader)
    {
        $this->trackReader = $trackReader;
    }

    public function setTrack(TrackInterface $track): PolylineGenerator
    {
        $this->track = $track;

        $this->trackReader->loadTrack($track);

        return $this;
    }

    public function processTrack(): TrackInterface
    {
        $this->track
            ->setPreviewPolyline($this->generatePreviewPolyline())
            ->setPolyline($this->generatePolyline())
        ;

        return $this->track;
    }

    public function generatePolyline(): string
    {
        $list = $this->trackReader->slicePublicCoords();

        $polyline = \Polyline::Encode($list);

        return $polyline;
    }

    public function generatePreviewPolyline(): string
    {
        $list = array_values($this->trackReader->slicePublicCoords());

        $tolerance = 0.01;
        $reducer = new RadialDistance($list);

        $reducedPointList = $reducer->reduce($tolerance);
        $reducedList = [];

        /** @var Point $point */
        foreach ($reducedPointList as $point) {
            $reducedList[] = [$point->x, $point->y];
        }

        $polyline = \Polyline::Encode($reducedList);

        return $polyline;
    }
}
