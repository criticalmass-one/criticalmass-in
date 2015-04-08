<?php

namespace Caldera\CriticalmassCoreBundle\Utility\LatLngArrayGenerator;

use Caldera\CriticalmassCoreBundle\Entity\Track;
use Caldera\CriticalmassCoreBundle\Utility\GpxReader\GpxReader;

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