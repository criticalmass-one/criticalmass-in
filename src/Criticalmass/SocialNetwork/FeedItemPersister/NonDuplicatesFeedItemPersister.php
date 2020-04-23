<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedItemPersister;

use App\Entity\SocialNetworkFeedItem;
use Doctrine\Persistence\ManagerRegistry;

class NonDuplicatesFeedItemPersister implements FeedItemPersisterInterface
{
    protected ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
    }

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

    protected function feedItemExists(SocialNetworkFeedItem $feedItem): bool
    {
        $existingItem = $this->doctrine->getRepository(SocialNetworkFeedItem::class)->findOneBy([
            'socialNetworkProfile' => $feedItem->getSocialNetworkProfile(),
            'uniqueIdentifier' => $feedItem->getUniqueIdentifier(),
        ]);

        return $existingItem !== null;
    }
}