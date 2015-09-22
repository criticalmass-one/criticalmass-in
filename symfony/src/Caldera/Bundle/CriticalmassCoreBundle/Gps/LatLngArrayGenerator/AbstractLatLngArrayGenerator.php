<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Gps\LatLngArrayGenerator;

use Caldera\Bundle\CriticalmassCoreBundle\Gps\GpxReader\GpxReader;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Track;

abstract class AbstractLatLngArrayGenerator
{
    protected $json;
    protected $track;
    protected $xmlRootNode;

    public function loadTrack(Track $track)
    {
        $this->track = $track;

        $gr = new GpxReader();
        $gr->loadTrack($this->track);

        $this->xmlRootNode = $gr->getRootNode();
    }

    public abstract function execute();

    public function getJsonArray()
    {
        return $this->json;
    }
} 