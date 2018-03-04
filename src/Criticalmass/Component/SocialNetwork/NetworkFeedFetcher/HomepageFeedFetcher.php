<?php

namespace Criticalmass\Component\SocialNetwork\NetworkFeedFetcher;

use Criticalmass\Bundle\AppBundle\Entity\SocialNetworkFeedItem;
use Criticalmass\Bundle\AppBundle\Entity\SocialNetworkProfile;
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

    protected function fetchFeed(SocialNetworkProfile $socialNetworkProfile): NetworkFeedFetcherInterface
    {
        $feedLink = $this->getFeedLink($socialNetworkProfile);

        if (!$feedLink) {
            return $this;
        }

        $feed = Reader::import($feedLink);

        /** @var EntryInterface $entry */
        foreach ($feed as $entry) {
            $feedItem = $this->convertEntryToFeedItem($entry);

            if ($feedItem) {
                $feedItem->setSocialNetworkProfile($socialNetworkProfile);

                $this->feedItemList[] = $feedItem;
            }
        }

        return $this;
    }

    protected function convertEntryToFeedItem(EntryInterface $entry): ?SocialNetworkFeedItem
    {
        $feedItem = new SocialNetworkFeedItem();

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
