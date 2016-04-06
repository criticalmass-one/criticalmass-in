<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Collector;

use Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item\RidePhotoItem;
use Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item\RideTrackItem;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Photo;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Track;

class RideTrackCollector extends AbstractTimelineCollector
{
    public function execute()
    {
        $trackEntities = $this->fetchEntities();
        $sortedEntities = $this->groupEntities($trackEntities);
        $this->convertGroupedEntities($sortedEntities);
    }

    protected function fetchEntities()
    {
        return $this->doctrine->getRepository('CalderaCriticalmassModelBundle:Track')->findForTimelineRideTrackCollector();
    }

    protected function groupEntities(array $trackEntities)
    {
        return $trackEntities;
    }

    protected function convertGroupedEntities(array $groupedEntities)
    {
        /**
         * @var Track $trackEntity
         */
        foreach ($groupedEntities as $trackEntity) {
            $item = new RideTrackItem();

            $item->setRide($trackEntity->getRide());
            $item->setTrack($trackEntity);
            $item->setUser($trackEntity->getUser());
            $item->setPolyline($trackEntity->getPolyline());

            array_push($this->items, $item);
        }
    }
}