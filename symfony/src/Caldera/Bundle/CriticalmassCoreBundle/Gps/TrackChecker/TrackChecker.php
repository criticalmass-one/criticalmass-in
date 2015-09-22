<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Gps\TrackChecker;

use Caldera\Bundle\CriticalmassModelBundle\Entity\Track;

class TrackChecker implements TrackCheckerInterface {
    protected $track;
    protected $result = true;
    
    public function __construct(Track $track)
    {
        $this->track = $track;
    }
    
    public function isValid()
    {
        return $this->result;
    }
    
    protected function checkPointsNumber()
    {
        
        
    }
    
    
}