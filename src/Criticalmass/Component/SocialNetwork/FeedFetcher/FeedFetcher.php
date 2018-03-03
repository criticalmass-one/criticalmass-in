<?php

namespace Criticalmass\Component\SocialNetwork\FeedFetcher;

use Criticalmass\Component\SocialNetwork\NetworkFeedFetcher\NetworkFeedFetcherInterface;

class FeedFetcher
{
    protected $networkFetcherList = [];

    public function __construct()
    {
    }

    public function addNetworkFeedFetcher(NetworkFeedFetcherInterface $networkFeedFetcher): FeedFetcher
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
        return [];
    }
}
