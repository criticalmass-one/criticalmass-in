<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Collector;

use App\Criticalmass\Timeline\Item\RideParticipationEstimateItem;
use App\Entity\RideEstimate;

class RideParticipationEstimateCollector extends AbstractTimelineCollector
{
    protected $entityClass = RideEstimate::class;

    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
    {
        /** @var RideEstimate $estimateEntity */
        foreach ($groupedEntities as $estimateEntity) {
            $item = new RideParticipationEstimateItem();

            $item
                ->setUser($estimateEntity->getUser())
                ->setRide($estimateEntity->getRide())
                ->setRideTitle($estimateEntity->getRide()->getTitle())
                ->setEstimatedParticipants($estimateEntity->getEstimatedParticipants())
                ->setDateTime($estimateEntity->getDateTime())
                ->setRideEnabled($estimateEntity->getRide()->isEnabled());

            $this->addItem($item);
        }

        return $this;
    }
}
