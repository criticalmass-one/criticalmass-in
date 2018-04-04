<?php

namespace Criticalmass\Component\Timeline\Collector;

use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Component\Timeline\Item\RideEditItem;

class RideEditCollector extends AbstractTimelineCollector
{
    protected $entityClass = Ride::class;

    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
    {
        /** @var Ride $ride */
        foreach ($groupedEntities as $ride) {
            $item = new RideEditItem();

            $item->setUser($ride->getUser());
            $item->setRideTitle($ride->getFancyTitle());
            $item->setRide($ride);
            $item->setDateTime($ride->getUpdatedAt());

            $this->addItem($item);
        }

        return $this;
    }
}
