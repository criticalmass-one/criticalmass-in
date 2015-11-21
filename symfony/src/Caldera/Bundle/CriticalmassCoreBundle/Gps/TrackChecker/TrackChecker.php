<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Gps\TrackChecker;

use Caldera\Bundle\CriticalmassCoreBundle\Gps\GpxReader\TrackReader;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Track;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class TrackChecker implements TrackCheckerInterface {
    protected $track;
    protected $result = true;
    
    public function __construct(UploaderHelper $uploaderHelper, TrackReader $trackReader, $rootDirectory)
    {


    }
    
    public function loadTrack(Track $track)
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