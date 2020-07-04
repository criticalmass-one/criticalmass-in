<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedFetcher\NetworkFeedFetcher\Homepage;

use App\Criticalmass\SocialNetwork\FeedFetcher\FetchInfo;
use App\Criticalmass\SocialNetwork\FeedFetcher\NetworkFeedFetcher\AbstractNetworkFeedFetcher;
use App\Criticalmass\SocialNetwork\FeedFetcher\NetworkFeedFetcher\NetworkFeedFetcherInterface;
use App\Entity\SocialNetworkProfile;
use Zend\Feed\Reader\Entry\EntryInterface;
use Zend\Feed\Reader\Reader;

class HomepageFeedFetcher extends AbstractNetworkFeedFetcher
{
    public function fetch(SocialNetworkProfile $socialNetworkProfile, FetchInfo $fetchInfo): NetworkFeedFetcherInterface
    {
        if (!$socialNetworkProfile->getCity()) {
            return $this;
        }

        try {
            $this->fetchFeed($socialNetworkProfile);
        } catch (\Exception $exception) {
            $this->markAsFailed($socialNetworkProfile, sprintf('Failed to fetch social network profile %d: %s', $socialNetworkProfile->getId(), $exception->getMessage()));
        }

        return $this;
    }


    protected function fetchFeed(SocialNetworkProfile $socialNetworkProfile): NetworkFeedFetcherInterface
    {
        $feedLink = FeedUriDetector::findFeedLink($socialNetworkProfile);

        if (!$feedLink) {
            return $this;
        }

        $this->logger->info(sprintf('Now quering %s', $feedLink));

        $feed = Reader::import($feedLink);

        /** @var EntryInterface $entry */
        foreach ($feed as $entry) {
            $feedItem = EntryConverter::convert($socialNetworkProfile, $entry);

            if ($feedItem) {
                $this->feedItemList[] = $feedItem;

                $this->logger->info(sprintf('Fetched website %s', $feedItem->getPermalink()));
            }
        }

        return $this;
    }

    protected function markAsFailed(SocialNetworkProfile $socialNetworkProfile, string $errorMessage): SocialNetworkProfile
    {
        $socialNetworkProfile
            ->setLastFetchFailureDateTime(new \DateTime())
            ->setLastFetchFailureError($errorMessage);

        $this
            ->logger
            ->notice($errorMessage);

        return $socialNetworkProfile;
    }
}
