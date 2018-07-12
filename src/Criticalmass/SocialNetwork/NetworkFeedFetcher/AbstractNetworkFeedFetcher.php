<?php

namespace AppBundle\Criticalmass\SocialNetwork\NetworkFeedFetcher;

abstract class AbstractNetworkFeedFetcher implements NetworkFeedFetcherInterface
{
    /** @var array $feedItemList */
    protected $feedItemList = [];

    public function getFeedItemList(): array
    {
        return $this->feedItemList;
    }
}
