<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedItemPersister;

use App\Entity\SocialNetworkFeedItem;

class NonDuplicatesFeedItemPersister extends FeedItemPersister
{
    public function persistFeedItemList(array $feedItemList): FeedItemPersisterInterface
    {
        $em = $this->doctrine->getManager();

        foreach ($feedItemList as $feedItem) {
            if (!$this->feedItemExists($feedItem)) {
                $em->persist($feedItem);
            }
        }

        try {
            $em->flush();
        } catch (\Exception $exception) {

        }

        return $this;
    }

    public function persistFeedItem(SocialNetworkFeedItem $socialNetworkFeedItem): FeedItemPersisterInterface
    {
        if ($this->feedItemExists($socialNetworkFeedItem)) {
            return $this;
        }
        
        $em = $this->doctrine->getManager();

        $em->persist($socialNetworkFeedItem);

        try {
            $em->flush();
        } catch (\Exception $exception) {

        }

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