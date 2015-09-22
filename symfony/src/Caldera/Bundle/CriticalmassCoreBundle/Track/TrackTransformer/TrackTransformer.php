<?php

namespace Caldera\CriticalmassTimelapseBundle\Utility\TrackTransformer;

use Caldera\CriticalmassCoreBundle\Utility\GpxReader\GpxReader;
use Caldera\CriticalmassTrackBundle\Entity\Track;

class TrackTransformer {
    protected $track;
    
    public function __construct()
    {
        
    }
    
    public function setTrack(Track $track)
    {
        $this->track = $track;
    }
    
    public function execute()
    {
        $gr = new GpxReader();
        $gr->loadTrack($this->track);
        
        $node = $gr->getRootNode();
        
        
        
        
    }
}