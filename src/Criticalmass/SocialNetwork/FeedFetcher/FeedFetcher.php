<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedFetcher;

use App\Criticalmass\SocialNetwork\NetworkFeedFetcher\NetworkFeedFetcherInterface;
use App\Entity\SocialNetworkFeedItem;
use App\Entity\SocialNetworkProfile;

class FeedFetcher extends AbstractFeedFetcher
{
    public function addNetworkFeedFetcher(NetworkFeedFetcherInterface $networkFeedFetcher): FeedFetcherInterface
    {
        $this->networkFetcherList[] = $networkFeedFetcher;

        return $this;
    }

    public function getNetworkFetcherList(): array
    {
        return $this->networkFetcherList;
    }

    protected function getSocialNetworkProfiles(): array
    {
        return $this->doctrine->getRepository(SocialNetworkProfile::class)->findAll();
    }

    protected function getFeedFetcherForNetworkProfile(SocialNetworkProfile $socialNetworkProfile): ?NetworkFeedFetcherInterface
    {
        /** @var NetworkFeedFetcherInterface $fetcher */
        foreach ($this->networkFetcherList as $fetcher) {
            if ($fetcher->supports($socialNetworkProfile)) {
                return $fetcher;
            }
        }

        return null;
    }

    public function fetch(): FeedFetcherInterface
    {
        $this->stripNetworkList();

        $profileList = $this->getSocialNetworkProfiles();

        foreach ($profileList as $profile) {
            $fetcher = $this->getFeedFetcherForNetworkProfile($profile);

            if ($fetcher) {
                $feedItemList = $fetcher->fetch($profile)->getFeedItemList();

                $this->feedItemList = array_merge($this->feedItemList, $feedItemList);
            }
        }

        return $this;
    }

    public function persist(): FeedFetcherInterface
    {
        $em = $this->doctrine->getManager();

        foreach ($this->feedItemList as $feedItem) {
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

    protected function stripNetworkList(): FeedFetcher
    {
        if (count($this->fetchableNetworkList) === 0) {
            return $this;
        }

        /** @var NetworkFeedFetcherInterface $fetcher */
        foreach ($this->networkFetcherList as $key => $fetcher) {
            if (!in_array($fetcher->getNetworkIdentifier(), $this->fetchableNetworkList)) {
                unset($this->networkFetcherList[$key]);
            }
        }

        return $this;
    }
}
