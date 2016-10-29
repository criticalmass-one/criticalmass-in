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
        if (!$this->northWest) {
            $this->northWest = new Coord($element->getLatitude(), $element->getLongitude());
        } else {
            if ($this->northWest->getLatitude() < $element->getLatitude()) {
                $this->northWest->setLatitude($element->getLatitude());
            }

            if ($this->northWest->getLongitude() < $element->getLongitude()) {
                $this->northWest->setLatitude($element->getLongitude());
            }
        }

        if ($this->southEast) {
            $this->southEast = new Coord($element->getLatitude(), $element->getLongitude());
        } 
        
    }
}