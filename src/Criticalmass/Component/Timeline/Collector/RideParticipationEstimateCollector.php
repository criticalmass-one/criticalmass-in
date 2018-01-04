<?php

namespace Criticalmass\Component\Timeline\Collector;

use Criticalmass\Component\Entity\RideEstimate;
use Criticalmass\Component\Timeline\Item\RideParticipationEstimateItem;

class RideParticipationEstimateCollector extends AbstractTimelineCollector
{
    protected $entityClass = RideEstimate::class;

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
