<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Collector;

use App\Entity\Ride;
use App\Criticalmass\Timeline\Item\RideEditItem;

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
                ->setRideTitle($ride->getTitle())
                ->setRide($ride)
                ->setDateTime($ride->getUpdatedAt());

            $this->addItem($item);
        }

        return $this;
    }
}
