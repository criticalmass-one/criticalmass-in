<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Collector;

use Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item\RideParticipationEstimateItem;
use Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item\RidePhotoItem;
use Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item\RideTrackItem;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Photo;
use Caldera\Bundle\CriticalmassModelBundle\Entity\RideEstimate;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Track;

class RideParticipationEstimateCollector extends AbstractTimelineCollector
{
    public function execute()
    {
        $entities = $this->fetchEntities();
        $sortedEntities = $this->groupEntities($entities);
        $this->convertGroupedEntities($sortedEntities);
    }

    protected function fetchEntities()
    {
        return $this->doctrine->getRepository('CalderaCriticalmassModelBundle:RideEstimate')->findForTimelineRideParticipantsEstimateCollector();
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
            $item->setUser($estimateEntity->getUser());
            $item->setEstimatedParticipants($estimateEntity->getEstimatedParticipants());
            $item->setDateTime($estimateEntity->getCreationDateTime());

            array_push($this->items, $item);
        }
    }
}