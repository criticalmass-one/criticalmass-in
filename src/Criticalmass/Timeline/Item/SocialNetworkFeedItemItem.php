<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Item;

use App\Entity\SocialNetworkFeedItem;

class SocialNetworkFeedItemItem extends AbstractItem
{
    /** @var SocialNetworkFeedItem $socialNetworkFeedItem */
    protected $socialNetworkFeedItem;

    public function getSocialNetworkFeedItem(): SocialNetworkFeedItem
    {
        return $this->socialNetworkFeedItem;
    }

    public function setSocialNetworkFeedItem(SocialNetworkFeedItem $socialNetworkFeedItem): SocialNetworkFeedItemItem
    {
        $this->socialNetworkFeedItem = $socialNetworkFeedItem;

        $this->tabName = $socialNetworkFeedItem->getSocialNetworkProfile()->getNetwork();
        
        return $this;
    }
}
