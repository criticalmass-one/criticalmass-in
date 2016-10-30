<?php

namespace Caldera\Bundle\CalderaBundle\MapPrinter\Canvas;

use Caldera\Bundle\CalderaBundle\Entity\Track;
use Caldera\Bundle\CalderaBundle\MapPrinter\Coord\Coord;
use Caldera\Bundle\CalderaBundle\MapPrinter\Element\MapElement;
use Caldera\Bundle\CalderaBundle\MapPrinter\Element\MarkerInterface;
use Caldera\Bundle\CalderaBundle\MapPrinter\Element\TrackInterface;
use Caldera\Bundle\CalderaBundle\MapPrinter\TileResolver\TileResolverInterface;
use Caldera\Bundle\CalderaBundle\MapPrinter\Util\OsmZxyCalculator;

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

    protected $grid = [];

    protected $canvasWidth;
    protected $canvasHeight;
    protected $offsetLeft;
    protected $offsetTop;
    
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

    public function decorateTiles(TileResolverInterface $tileResolver): Canvas
    {
        $zoomLevel = 15;

        $topY = OsmZxyCalculator::latitudeToOSMYTile($this->northWest->getLatitude(), $zoomLevel);
        $topX = OsmZxyCalculator::longitudeToOSMXTile($this->northWest->getLongitude(), $zoomLevel);

        $bottomY = OsmZxyCalculator::latitudeToOSMYTile($this->southEast->getLatitude(), $zoomLevel);
        $bottomX = OsmZxyCalculator::longitudeToOSMXTile($this->southEast->getLongitude(), $zoomLevel);

        for ($y = $topY; $y <= $bottomY; ++$y) {
            for ($x = $topX; $x <= $bottomX; ++$x) {
                $this->grid[$y][$x] = $tileResolver->resolveByZxy($x, $y, $zoomLevel);
            }
        }

        $this->canvasWidth = abs($topX - $bottomX);
        $this->canvasHeight = abs($topY - $bottomY);
        $this->offsetLeft = $topX;
        $this->offsetTop = $topY;

        return $this;
    }

    public function printElements(): Canvas
    {
        $height = $this->canvasHeight * 256;
        $width = $this->canvasWidth * 256;

        $image = imagecreate($width, $height);

        $white = imagecolorallocatealpha($image, 255, 255, 0, 100);

        foreach ($this->tracks as $track) {
            $coordList = $this->convertTrackToCoordArray($track);

            $coordA = array_shift($coordList);

            while ($coordB = array_shift($coordList)) {
                imageline($image, 0, 0, 100, 100, $white);

                $coordA = $coordB;
            }

        }
        imagepng($image);
        imagedestroy($image);
        
        return $this;
    }
}