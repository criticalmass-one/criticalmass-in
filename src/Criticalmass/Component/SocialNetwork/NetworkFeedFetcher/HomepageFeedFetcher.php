<?php

namespace Criticalmass\Component\SocialNetwork\NetworkFeedFetcher;

use Criticalmass\Bundle\AppBundle\Entity\SocialNetworkProfile;
use Criticalmass\Component\SocialNetwork\Entity\FeedItem;
use Zend\Feed\Reader\Entry\EntryInterface;
use Zend\Feed\Reader\Reader;

class HomepageFeedFetcher extends AbstractNetworkFeedFetcher
{
    public function __construct()
    {

    }

    public function fetch(SocialNetworkProfile $socialNetworkProfile): NetworkFeedFetcherInterface
    {
        $this->fetchFeed($socialNetworkProfile);

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

    protected function fetchFeed(SocialNetworkProfile $socialNetworkProfile): array
    {
        $feedLink = $this->getFeedLink($socialNetworkProfile);
        $feed = Reader::import($feedLink);

        $feedItemList = [];

        /** @var EntryInterface $entry */
        foreach ($feed as $entry) {
            $feedItem = $this->convertEntryToFeedItem($entry);

            if ($feedItem) {
                $feedItem->setSocialNetworkProfile($socialNetworkProfile);

                $feedItemList[] = $feedItem;
            }
        }

        return $feedItemList;
    }

    protected function convertEntryToFeedItem(EntryInterface $entry): ?FeedItem
    {
        $feedItem = new FeedItem();

        try {
            $feedItem
                ->setUniqueIdentifier($entry->getId())
                ->setTitle($entry->getTitle())
                ->setText($entry->getContent())
                ->setDateTime($entry->getDateCreated());
        } catch (\Exception $e) {
            return null;
        }

        return $feedItem;
    }
}
