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

        return $this;
    }

    /**
     * The timeline groups its items into one tab per tab name, so a feed item
     * has to carry the identifier of the network it was posted on. Without a
     * known network it joins the general news tab.
     */
    public function setNetwork(?string $network): self
    {
        $this->tabName = $network ?? 'standard';

        return $this;
    }
}
