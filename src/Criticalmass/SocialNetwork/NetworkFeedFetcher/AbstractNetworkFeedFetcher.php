<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\NetworkFeedFetcher;

abstract class AbstractNetworkFeedFetcher implements NetworkFeedFetcherInterface
{
    /** @var array $feedItemList */
    protected $feedItemList = [];

    public function getFeedItemList(): array
    {
        return $this->feedItemList;
    }
}
