<?php

namespace Criticalmass\Component\Timeline\Collector;

use Criticalmass\Bundle\AppBundle\Entity\Track;
use Criticalmass\Component\Timeline\Item\RideTrackItem;

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
            $item->setUsername($trackEntity->getUser()->getUsername());
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
