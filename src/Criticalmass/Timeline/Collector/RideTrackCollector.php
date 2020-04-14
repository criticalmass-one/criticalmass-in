<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Collector;

use App\Criticalmass\Timeline\Item\RideTrackItem;
use App\Entity\Track;

class RideTrackCollector extends AbstractTimelineCollector
{
    protected $entityClass = Track::class;

    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
    {
        /** @var Track $trackEntity */
        foreach ($groupedEntities as $trackEntity) {
            $item = new RideTrackItem();

            $item
                ->setUser($trackEntity->getUser())
                ->setRide($trackEntity->getRide())
                ->setTrack($trackEntity)
                ->setRideTitle($trackEntity->getRide()->getTitle())
                ->setDistance($trackEntity->getDistance())
                ->setDuration($trackEntity->getDurationInSeconds())
                ->setPolyline($trackEntity->getPolyline())
                ->setPolylineColor('rgb(' . $trackEntity->getUser()->getColorRed() . ', ' . $trackEntity->getUser()->getColorGreen() . ', ' . $trackEntity->getUser()->getColorBlue() . ')')
                ->setDateTime($trackEntity->getCreationDateTime())
                ->setRideEnabled($trackEntity->getRide()->isEnabled());

            $this->addItem($item);
        }

        return $this;
    }
}
