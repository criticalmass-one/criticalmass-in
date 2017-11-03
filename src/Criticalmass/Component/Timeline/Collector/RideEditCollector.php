<?php

namespace Criticalmass\Bundle\AppBundle\Timeline\Collector;

use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Bundle\AppBundle\Timeline\Item\RideEditItem;

class RideEditCollector extends AbstractTimelineCollector
{
    protected $entityClass = Ride::class;

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
