<?php

namespace AppBundle\Criticalmass\Gps\LatLngListGenerator;

use AppBundle\Entity\Track;
use AppBundle\Criticalmass\Gps\GpxReader\TrackReader;

/**
 * @deprecated
 */
abstract class AbstractLatLngListGenerator
{
    protected $list;

    /**
     * @var Track $track
     */
    protected $track;
    protected $xmlRootNode;
    protected $trackReader;
    protected $gapWidth;

    public function __construct(TrackReader $trackReader, $gapWidth)
    {
        $this->trackReader = $trackReader;
        $this->gapWidth = $gapWidth;
    }

    public function loadTrack(Track $track)
    {
        $this->track = $track;

        $this->trackReader->loadTrack($this->track);

        $this->xmlRootNode = $this->trackReader->getRootNode();

        return $this;
    }

    public abstract function execute();

    public function getList()
    {
        return $this->list;
    }
} 
