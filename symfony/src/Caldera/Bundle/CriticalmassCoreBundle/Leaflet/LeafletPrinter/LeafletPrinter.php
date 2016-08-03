<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Leaflet\LeafletPrinter;

use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Caldera\Bundle\CalderaBundle\Entity\Track;
use Caldera\Bundle\CriticalmassCoreBundle\Gps\BoundingBox;
use Caldera\Bundle\CriticalmassCoreBundle\Gps\Coord;
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
        $this->calculateTileSize();
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
        $northTileNumber = null;
        $westTileNumber = null;
        $southTileNumber = null;
        $eastTileNumber = null;

        /** @var BoundingBox $boundingBox */
        foreach ($this->boundingBoxes as $boundingBox) {
            /** @var Coord $northWest */
            $northWest = $boundingBox->getNorthWest();

            /** @var Coord $southEast */
            $southEast = $boundingBox->getSouthEast();

            $boxNorthTileNumber = OSMCoordCalculator::latitudeToOSMYTile($northWest->getLatitude(), 15);
            $boxWestTileNumber = OSMCoordCalculator::longitudeToOSMXTile($northWest->getLongitude(), 15);
            $boxSouthTileNumber = OSMCoordCalculator::latitudeToOSMYTile($southEast->getLatitude(), 15);
            $boxEastTileNumber = OSMCoordCalculator::longitudeToOSMXTile($southEast->getLongitude(), 15);

            if (!$northTileNumber or $northTileNumber < $boxNorthTileNumber) {
                $northTileNumber = $boxNorthTileNumber;
            }

            if (!$westTileNumber or $westTileNumber < $boxWestTileNumber) {
                $westTileNumber = $boxWestTileNumber;
            }

            if (!$southTileNumber or $southTileNumber > $boxSouthTileNumber) {
                $southTileNumber = $boxSouthTileNumber;
            }

            if (!$eastTileNumber or $eastTileNumber > $boxEastTileNumber) {
                $eastTileNumber = $boxEastTileNumber;
            }

            var_dump($northTileNumber);
            var_dump($westTileNumber);
            var_dump($southTileNumber);
            var_dump($eastTileNumber);

        }
    }
}