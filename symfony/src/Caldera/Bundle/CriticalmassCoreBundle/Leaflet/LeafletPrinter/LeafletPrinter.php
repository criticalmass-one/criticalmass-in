<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Leaflet\LeafletPrinter;

use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Caldera\Bundle\CalderaBundle\Entity\Track;
use Caldera\Bundle\CriticalmassCoreBundle\Gps\GpxReader\TrackReader;
use Doctrine\Bundle\DoctrineBundle\Registry;

class LeafletPrinter extends AbstractLeafletPrinter
{
    /** @var Registry $doctrine */
    protected $doctrine;

    /** @var TrackReader $trackReader */
    protected $trackReader;

    /** @var array $tracks */
    protected $tracks;

    /** @var Ride $ride */
    protected $ride;

    /** @var array $boundingBoxes */
    protected $boundingBoxes = [];
    
    public function __construct(Registry $doctrine, TrackReader $trackReader)
    {
        $this->doctrine = $doctrine;
        $this->trackReader = $trackReader;
    }

    public function setRide(Ride $ride)
    {
        $this->ride = $ride;
    }

    public function execute()
    {
        $this->collectTracks();
        $this->calculateBoundingBoxes();
    }

    protected function collectTracks()
    {
        $this->tracks = $this->doctrine->getRepository('CalderaBundle:Track')->findTracksByRide($this->ride);
    }

    protected function calculateBoundingBoxes()
    {
        /** @var Track $track */
        foreach ($this->tracks as $track) {
            $this->trackReader->loadTrack($track);

            $this->boundingBoxes[$track->getId()] = $this->trackReader->getBoundingBoxes();
        }
    }

    protected function calculateTileSize()
    {
        OSMCoordCalculator
    }
}