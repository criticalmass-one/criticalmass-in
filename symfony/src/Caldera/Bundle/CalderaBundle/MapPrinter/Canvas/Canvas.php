<?php

namespace Caldera\Bundle\CalderaBundle\MapPrinter\Canvas;

use Caldera\Bundle\CalderaBundle\MapPrinter\Element\Marker;
use Caldera\Bundle\CalderaBundle\MapPrinter\Element\Track;

class Canvas
{
    /** @var array $markers */
    protected $markers = [];

    /** @var array $tracks */
    protected $tracks = [];
    
    public function __construct()
    {
        
    }
    
    public function addMarker(Marker $marker): Canvas
    {
        array_push($this->markers, $marker);

        return $this;    
    }

    public function addTrack(Track $track): Canvas
    {
        array_push($this->tracks, $track);

        return $this;
    }
}