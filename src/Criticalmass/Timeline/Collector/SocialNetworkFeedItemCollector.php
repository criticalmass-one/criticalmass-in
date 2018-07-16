<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Collector;

use App\Entity\SocialNetworkFeedItem;
use App\Criticalmass\Timeline\Item\SocialNetworkFeedItemItem;

class SocialNetworkFeedItemCollector extends AbstractTimelineCollector
{
    protected $entityClass = SocialNetworkFeedItem::class;

    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
    {
        return $this;
        
        /** @var SocialNetworkFeedItemItem $itemEntity */
        foreach ($groupedEntities as $itemEntity) {
            $item = new SocialNetworkFeedItemItem();

            $item
                ->setUser($itemEntity->getSocialNetworkProfile()->getUser())
                ->setCity($itemEntity->getSocialNetworkProfile()->getCity())
                ->setRide($itemEntity->getSocialNetworkProfile()->getRide())
                ->setSubride($itemEntity->getSocialNetworkProfile()->getSubride())
                ->setPermalink($itemEntity->getPermalink())
                ->setTitle($itemEntity->getTitle())
                ->setText($itemEntity->getText())
                ->setDateTime($itemEntity->getDateTime());

            $this->addItem($item);
        }

        return $this;
    }
}
