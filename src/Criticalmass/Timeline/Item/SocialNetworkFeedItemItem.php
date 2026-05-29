<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Item;

use App\Criticalmass\SocialNetwork\FeedsApi\Dto\FeedItem;

class SocialNetworkFeedItemItem extends AbstractItem
{
    protected ?FeedItem $feedItem = null;

    public function getFeedItem(): ?FeedItem
    {
        return $this->feedItem;
    }

    public function setFeedItem(FeedItem $feedItem): self
    {
        $this->feedItem = $feedItem;

        $dateTime = \DateTime::createFromInterface($feedItem->getDateTime());
        $this->setDateTime($dateTime);

        $this->tabName = 'social_feed';

        return $this;
    }
}
