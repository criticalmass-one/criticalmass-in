<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedItemPersister;

interface FeedItemPersisterInterface
{
    public function persistFeedItemList(array $feedItemList): FeedItemPersisterInterface;
}