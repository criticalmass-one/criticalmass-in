<?php

namespace Criticalmass\Component\Timeline\Collector;

use Criticalmass\Bundle\AppBundle\Entity\SocialNetworkFeedItem;
use Criticalmass\Component\Timeline\Item\SocialNetworkFeedItemItem;

class SocialNetworkFeedItemCollector extends AbstractTimelineCollector
{
    protected $entityClass = SocialNetworkFeedItem::class;

    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
    {
        /** @var SocialNetworkFeedItemItem $itemEntity */
        foreach ($groupedEntities as $itemEntity) {
            $item = new SocialNetworkFeedItemItem();

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
