<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedFetcher;

use App\Criticalmass\SocialNetwork\NetworkFeedFetcher\NetworkFeedFetcherInterface;

interface FeedFetcherInterface
{
    public function addNetworkFeedFetcher(NetworkFeedFetcherInterface $networkFeedFetcher): FeedFetcherInterface;

    public function getNetworkFetcherList(): array;

    public function fetch(): FeedFetcherInterface;

    public function persist(): FeedFetcherInterface;
}
