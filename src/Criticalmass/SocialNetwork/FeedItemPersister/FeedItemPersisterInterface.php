<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedItemPersister;

use App\Entity\SocialNetworkFeedItem;

interface FeedItemPersisterInterface
{
    public function persistFeedItemList(array $feedItemList): FeedItemPersisterInterface;

    public function persistFeedItem(SocialNetworkFeedItem $feedItem): FeedItemPersisterInterface;

    public function flush(): FeedItemPersisterInterface;
}