<?php

namespace Caldera\Bundle\CalderaBundle\MapPrinter\Canvas;

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
        foreach ($this->markers as $marker) {

        }
    }

    protected function expand(MapElement $element): Canvas
    {
        $coord = new Coord($element->getLatitude(), $element->getLongitude());

        if (!$this->northWest) {
            $this->northWest = $coord;
        } else {
            if ($this->northWest->southOf($coord)) {
                $this->northWest->setLatitude($coord->getLatitude());
            }

            if ($this->northWest->eastOf($coord)) {
                $this->northWest->setLongitude($coord->getLongitude());
            }
        }

        if ($this->southEast) {
            $this->southEast = $coord;
        } else {
            if ($this->southEast->northOf($coord)) {
                $this->southEast->setLatitude($coord->getLatitude());
            }

            if ($this->southEast->westOf($coord)) {
                $this->southEast->setLongitude($coord->getLongitude());
            }
        }

        
    }
}