<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Collector;

use App\Entity\SocialNetworkFeedItem;
use App\Criticalmass\Timeline\Item\SocialNetworkFeedItemItem;

class SocialNetworkFeedItemCollector extends AbstractTimelineCollector
{
    protected $entityClass = SocialNetworkFeedItem::class;

    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
    {
        //return $this;
        
        /** @var SocialNetworkFeedItem $itemEntity */
        foreach ($groupedEntities as $itemEntity) {
            $item = new SocialNetworkFeedItemItem();

            $item
                ->setSocialNetworkFeedItem($itemEntity)
                ->setDateTime($itemEntity->getDateTime());

            $this->addItem($item);
        }

        return $this;
    }
}
