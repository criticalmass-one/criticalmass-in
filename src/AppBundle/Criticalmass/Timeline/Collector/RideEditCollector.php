<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Timeline\Collector;

use AppBundle\Entity\Ride;
use AppBundle\Criticalmass\Timeline\Item\RideEditItem;

class RideEditCollector extends AbstractTimelineCollector
{
    protected $entityClass = Ride::class;

    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
    {
        /** @var Ride $ride */
        foreach ($groupedEntities as $ride) {
            $item = new RideEditItem();

            $item
                ->setUser($ride->getUser())
                ->setUsername($ride->getUser()->getUsername())
                ->setRideTitle($ride->getFancyTitle())
                ->setRide($ride)
                ->setDateTime($ride->getUpdatedAt());

            $this->addItem($item);
        }

        return $this;
    }
}
