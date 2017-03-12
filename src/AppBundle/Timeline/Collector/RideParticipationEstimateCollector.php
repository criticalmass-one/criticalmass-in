<?php

namespace AppBundle\Timeline\Collector;

use AppBundle\Entity\RideEstimate;
use AppBundle\Timeline\Item\RideParticipationEstimateItem;

class RideParticipationEstimateCollector extends AbstractTimelineCollector
{
    protected function fetchEntities()
    {
        return $this->doctrine->getRepository('AppBundle:RideEstimate')->findForTimelineRideParticipantsEstimateCollector($this->startDateTime, $this->endDateTime);
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