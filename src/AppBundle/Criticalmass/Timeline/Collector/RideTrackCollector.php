<?php

namespace AppBundle\Criticalmass\Timeline\Collector;

use AppBundle\Entity\Track;
use AppBundle\Criticalmass\Timeline\Item\RideTrackItem;

class RideTrackCollector extends AbstractTimelineCollector
{
    protected $entityClass = Track::class;

    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
    {
        /** @var Track $trackEntity */
        foreach ($groupedEntities as $trackEntity) {
            $item = new RideTrackItem();

            $item->setRide($trackEntity->getRide());
            $item->setTrack($trackEntity);
            $item->setRideTitle($trackEntity->getRide()->getFancyTitle());
            $item->setUser($trackEntity->getUser());
            $item->setDistance($trackEntity->getDistance());
            $item->setDuration($trackEntity->getDurationInSeconds());
            $item->setPolyline($trackEntity->getPolyline());
            $item->setPolylineColor('rgb(' . $trackEntity->getUser()->getColorRed() . ', ' . $trackEntity->getUser()->getColorGreen() . ', ' . $trackEntity->getUser()->getColorBlue() . ')');
            $item->setDateTime($trackEntity->getCreationDateTime());

            $this->addItem($item);
        }

        return $this;
    }
}
