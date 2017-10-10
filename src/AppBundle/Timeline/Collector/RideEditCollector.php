<?php

namespace AppBundle\Timeline\Collector;

use AppBundle\Entity\Ride;
use AppBundle\Timeline\Item\RideEditItem;

class RideEditCollector extends AbstractTimelineCollector
{
    protected function fetchEntities()
    {
        return $this->doctrine->getRepository('AppBundle:Ride')->findForTimelineRideEditCollector($this->startDateTime, $this->endDateTime);
    }

    protected function groupEntities(array $entities)
    {
        return $entities;
    }

    protected function convertGroupedEntities(array $groupedEntities)
    {
        /**
         * @var Ride $ride
         */
        foreach ($groupedEntities as $ride) {
            $item = new RideEditItem();

            $item->setUsername($ride->getUser()->getUsername());
            $item->setRideTitle($ride->getFancyTitle());
            $item->setRide($ride);
            $item->setDateTime($ride->getUpdatedAt());

            $this->addItem($item);
        }
    }
}