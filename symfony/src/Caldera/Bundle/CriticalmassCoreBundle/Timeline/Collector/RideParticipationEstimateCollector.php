<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Collector;

use Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item\RideParticipationEstimateItem;
use Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item\RidePhotoItem;
use Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item\RideTrackItem;
use Caldera\Bundle\CalderaBundle\Entity\Photo;
use Caldera\Bundle\CalderaBundle\Entity\RideEstimate;
use Caldera\Bundle\CalderaBundle\Entity\Track;

class RideParticipationEstimateCollector extends AbstractTimelineCollector
{
    protected function fetchEntities()
    {
        return $this->doctrine->getRepository('CalderaBundle:RideEstimate')->findForTimelineRideParticipantsEstimateCollector($this->startDateTime, $this->endDateTime);
    }

    protected function groupEntities(array $entities)
    {
        return $entities;
    }

    protected function convertGroupedEntities(array $groupedEntities)
    {
        /**
         * @var RideEstimate $estimateEntity
         */
        foreach ($groupedEntities as $estimateEntity) {
            $item = new RideParticipationEstimateItem();

            $item->setRide($estimateEntity->getRide());
            $item->setRideTitle($estimateEntity->getRide()->getFancyTitle());
            $item->setUsername($estimateEntity->getUser()->getUsername());
            $item->setEstimatedParticipants($estimateEntity->getEstimatedParticipants());
            $item->setDateTime($estimateEntity->getCreationDateTime());

            $this->addItem($item);
        }
    }
}