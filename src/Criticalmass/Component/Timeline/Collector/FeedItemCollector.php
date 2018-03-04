<?php

namespace Criticalmass\Component\Timeline\Collector;

use Criticalmass\Bundle\AppBundle\Entity\FeedItem;
use Criticalmass\Component\Timeline\Item\FeedItemItem;

class FeedItemCollector extends AbstractTimelineCollector
{
    protected $entityClass = FeedItem::class;

    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
    {
        /** @var FeedItemItem $itemEntity */
        foreach ($groupedEntities as $itemEntity) {
            $item = new FeedItemItem();

            $item
                ->setUser($itemEntity->getSocialNetworkProfile()->getUser())
                ->setCity($itemEntity->getSocialNetworkProfile()->getCity())
                ->setRide($itemEntity->getSocialNetworkProfile()->getRide())
                ->setSubride($itemEntity->getSocialNetworkProfile()->getSubride())
                ->setTitle($itemEntity->getTitle())
                ->setText($itemEntity->getText())
                ->setDateTime($itemEntity->getDateTime());

            $this->addItem($item);
        }

        return $this;
    }
}
