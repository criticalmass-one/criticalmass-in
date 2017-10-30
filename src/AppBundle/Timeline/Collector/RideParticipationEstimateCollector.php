<?php

namespace AppBundle\Timeline\Collector;

use AppBundle\Entity\RideEstimate;
use AppBundle\Timeline\Item\RideParticipationEstimateItem;

class RideParticipationEstimateCollector extends AbstractTimelineCollector
{
    private $entityClass = RideEstimate::class;

    protected function fetchEntities(): array
    {
        return $this->doctrine->getRepository(RideEstimate::class)->findForTimelineRideParticipantsEstimateCollector($this->startDateTime, $this->endDateTime);
    }

    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
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
            $item->setDateTime($estimateEntity->getDateTime());

            $this->addItem($item);
        }

        return $this;
    }
}
