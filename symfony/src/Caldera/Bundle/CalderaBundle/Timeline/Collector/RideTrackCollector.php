<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Collector;

use Caldera\Bundle\CalderaBundle\Entity\Track;
use Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item\RideTrackItem;

class RideTrackCollector extends AbstractTimelineCollector
{
    protected function fetchEntities()
    {
        return $this->doctrine->getRepository('CalderaBundle:Track')->findForTimelineRideTrackCollector($this->startDateTime, $this->endDateTime);
    }

    protected function groupEntities(array $entities)
    {
        return $entities;
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
            $item->setRideTitle($trackEntity->getRide()->getFancyTitle());
            $item->setUsername($trackEntity->getUser()->getUsername());
            $item->setDistance($trackEntity->getDistance());
            $item->setDuration($trackEntity->getDurationInSeconds());
            $item->setPolyline($trackEntity->getPolyline());
            $item->setPolylineColor('rgb(' . $trackEntity->getUser()->getColorRed() . ', ' . $trackEntity->getUser()->getColorGreen() . ', ' . $trackEntity->getUser()->getColorBlue() . ')');
            $item->setDateTime($trackEntity->getCreationDateTime());

            $this->addItem($item);
        }
    }
}