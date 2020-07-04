<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedItemPersister;

use App\Entity\SocialNetworkFeedItem;
use Doctrine\Persistence\ManagerRegistry;

class FeedItemPersister implements FeedItemPersisterInterface
{
    protected ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function persistFeedItemList(array $feedItemList): FeedItemPersisterInterface
    {
        foreach ($feedItemList as $feedItem) {
            $this->persistFeedItem($feedItem);
        }

        return $this;
    }

    public function persistFeedItem(SocialNetworkFeedItem $socialNetworkFeedItem): FeedItemPersisterInterface
    {
        $this->doctrine->getManager()->persist($socialNetworkFeedItem);

        return $this;
    }

    public function flush(): FeedItemPersisterInterface
    {
        try {
            $this->doctrine->getManager()->flush();
        } catch (\Exception $exception) {
            //$this->doctrine->resetManager();
        }

        return $this;
    }
}