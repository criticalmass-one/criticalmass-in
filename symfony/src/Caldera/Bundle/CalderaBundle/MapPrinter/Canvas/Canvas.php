<?php

namespace Caldera\Bundle\CalderaBundle\MapPrinter\Canvas;

use Caldera\Bundle\CalderaBundle\Entity\Track;
use Caldera\Bundle\CalderaBundle\MapPrinter\Coord\Coord;
use Caldera\Bundle\CalderaBundle\MapPrinter\Element\MapElement;
use Caldera\Bundle\CalderaBundle\MapPrinter\Element\MarkerInterface;
use Caldera\Bundle\CalderaBundle\MapPrinter\Element\TrackInterface;

class Canvas
{
    /** @var Coord $northWest */
    protected $northWest = null;

    /** @var Coord $southEast */
    protected $southEast = null;

    /** @var array $markers */
    protected $markers = [];

    /** @var array $tracks */
    protected $tracks = [];
    
    public function __construct()
    {
    }
    
    public function addMarker(MarkerInterface $marker): Canvas
    {
        array_push($this->markers, $marker);

        return $this;    
    }

    public function addTrack(TrackInterface $track): Canvas
    {
        array_push($this->tracks, $track);

        return $this;
    }

    public function calculateDimensions(): Canvas
    {
        /** @var MarkerInterface $marker */
        foreach ($this->markers as $marker) {
            $coord = new Coord($marker->getLatitude(), $marker->getLongitude());
            $this->expand($coord);
        }
        
        /** @var TrackInterface $track */
        foreach ($this->tracks as $track) {
            $coordList = $this->convertTrackToCoordArray($track);

            /** @var Coord $coord */
            foreach ($coordList as $coord) {
                $this->expand($coord);
            }
        }
        
        return $this;
    }

    protected function convertTrackToCoordArray(TrackInterface $track): array
    {
        $pointList = \Polyline::decode($track->getPolyline());
        $coordList = [];

        while (count($pointList) > 0) {
            $latitude = array_shift($pointList);
            $longitude = array_shift($pointList);

            $coord = new Coord($latitude, $longitude);

            array_push($coordList, $coord);
        }

        return $coordList;
    }

    protected function expand(Coord $coord): Canvas
    {
        if (!$this->northWest) {
            $this->northWest = clone $coord;
        } else {
            if ($this->northWest->southOf($coord)) {
                $this->northWest->setLatitude($coord->getLatitude());
            }

            if ($this->northWest->eastOf($coord)) {
                $this->northWest->setLongitude($coord->getLongitude());
            }
        }

        if (!$this->southEast) {
            $this->southEast = clone $coord;
        } else {
            if ($this->southEast->northOf($coord)) {
                $this->southEast->setLatitude($coord->getLatitude());
            }

            if ($this->southEast->westOf($coord)) {
                $this->southEast->setLongitude($coord->getLongitude());
            }
        }

        return $this;
    }
}