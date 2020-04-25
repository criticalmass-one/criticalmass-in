<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedFetcher\NetworkFeedFetcher\Homepage;

use App\Criticalmass\SocialNetwork\FeedFetcher\FetchInfo;
use App\Criticalmass\SocialNetwork\FeedFetcher\NetworkFeedFetcher\AbstractNetworkFeedFetcher;
use App\Criticalmass\SocialNetwork\FeedFetcher\NetworkFeedFetcher\NetworkFeedFetcherInterface;
use App\Entity\SocialNetworkFeedItem;
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

    protected function getFeedLink(SocialNetworkProfile $socialNetworkProfile): ?string
    {
        $homepageAddress = $socialNetworkProfile->getIdentifier();

        $links = Reader::findFeedLinks($homepageAddress);

        if (isset($links->rdf)) {
            return $links->rdf;
        }

        if (isset($links->rss)) {
            return $links->rss;
        }

        if (isset($links->atom)) {
            return $links->atom;
        }

        return null;
    }

    protected function fetchFeed(SocialNetworkProfile $socialNetworkProfile): NetworkFeedFetcherInterface
    {
        $feedLink = $this->getFeedLink($socialNetworkProfile);

        if (!$feedLink) {
            return $this;
        }

        $this->logger->info(sprintf('Now quering %s', $feedLink));

        $feed = Reader::import($feedLink);

        /** @var EntryInterface $entry */
        foreach ($feed as $entry) {
            $feedItem = $this->convertEntryToFeedItem($entry);

            if ($feedItem) {
                $feedItem->setSocialNetworkProfile($socialNetworkProfile);

                $this->feedItemList[] = $feedItem;

                $this->logger->info(sprintf('Fetched website %s', $feedItem->getPermalink()));
            }
        }

        return $this;
    }

    protected function convertEntryToFeedItem(EntryInterface $entry): ?SocialNetworkFeedItem
    {
        $feedItem = new SocialNetworkFeedItem();

        try {
            $uniqueId = $entry->getId();
            $permalink = $entry->getPermalink();
            $title = $entry->getTitle();
            $text = $entry->getContent();
            $dateTime = $entry->getDateCreated();

            if ($uniqueId && $permalink && $title && $text && $dateTime) {
                $feedItem
                    ->setUniqueIdentifier($uniqueId)
                    ->setPermalink($permalink)
                    ->setTitle($title)
                    ->setText($text)
                    ->setDateTime($dateTime);

                return $feedItem;
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
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
