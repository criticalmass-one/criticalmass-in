<?php

namespace Caldera\Bundle\CalderaBundle\MapPrinter\Canvas;

use Caldera\Bundle\CalderaBundle\MapPrinter\Element\MarkerInterface;
use Caldera\Bundle\CalderaBundle\MapPrinter\Element\TrackInterface;

class Canvas
{
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
}