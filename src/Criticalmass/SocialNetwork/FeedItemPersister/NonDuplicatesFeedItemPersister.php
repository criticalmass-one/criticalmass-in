<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedItemPersister;

use App\Entity\SocialNetworkFeedItem;

class NonDuplicatesFeedItemPersister extends FeedItemPersister
{
    public function persistFeedItem(SocialNetworkFeedItem $socialNetworkFeedItem): FeedItemPersisterInterface
    {
        if ($this->feedItemExists($socialNetworkFeedItem)) {
            return $this;
        }

        $this->doctrine->getManager()->persist($socialNetworkFeedItem);
        
        return $this;
    }

    protected function feedItemExists(SocialNetworkFeedItem $feedItem): bool
    {
        $existingItem = $this->doctrine->getRepository(SocialNetworkFeedItem::class)->findOneBy([
            'socialNetworkProfile' => $feedItem->getSocialNetworkProfile(),
            'uniqueIdentifier' => $feedItem->getUniqueIdentifier(),
        ]);

        return $existingItem !== null;
    }
}