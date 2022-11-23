<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Collector;

use App\Criticalmass\Timeline\Item\RideEditItem;
use App\Entity\Ride;

class RideEditCollector extends AbstractTimelineCollector
{
    protected string $entityClass = Ride::class;

    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
    {
        /** @var Ride $ride */
        foreach ($groupedEntities as $ride) {
            $item = new RideEditItem();

            $item
                ->setUser($ride->getUser())
                ->setRideTitle($ride->getTitle())
                ->setRide($ride)
                ->setDateTime($ride->getUpdatedAt())
                ->setEnabled($ride->isEnabled());

            $this->addItem($item);
        }

        return $this;
    }
}
