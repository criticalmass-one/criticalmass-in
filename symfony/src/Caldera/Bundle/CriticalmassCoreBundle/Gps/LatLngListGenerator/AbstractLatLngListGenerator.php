<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Gps\LatLngArrayGenerator;

use Caldera\Bundle\CriticalmassCoreBundle\Gps\GpxReader\GpxReader;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Track;

abstract class AbstractLatLngListGenerator
{
    protected $json;
    protected $track;
    protected $xmlRootNode;

    public function __construct(UploaderHelper $uploaderHelper)
    {
        $this->uploaderHelper = $uploaderHelper;
    }
    
    public function loadTrack(Track $track)
    {
        $this->track = $track;

        echo $track->getTrackFilename()."BEWTWWRGERGWERG";
        $gr = new GpxReader();
        $gr->loadTrack($this->track);

        $this->xmlRootNode = $gr->getRootNode();
        
        echo $this->xmlRootNode;
    }

    public abstract function execute();

    public function getJsonArray()
    {
        return $this->json;
    }
} 