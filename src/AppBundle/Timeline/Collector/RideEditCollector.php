<?php

namespace AppBundle\Timeline\Collector;

use AppBundle\Entity\Ride;
use AppBundle\Timeline\Item\RideEditItem;

class RideEditCollector extends AbstractTimelineCollector
{
    private $entityClass = Ride::class;

    protected function fetchEntities(): array
    {
        return $this->doctrine->getRepository(Ride::class)->findForTimelineRideEditCollector($this->startDateTime, $this->endDateTime);
    }

    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
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

        return $this;
    }
}