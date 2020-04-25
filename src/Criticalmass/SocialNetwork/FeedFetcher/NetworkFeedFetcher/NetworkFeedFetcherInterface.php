<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedFetcher\NetworkFeedFetcher;

use App\Criticalmass\SocialNetwork\FeedFetcher\FetchInfo;
use App\Entity\SocialNetworkProfile;

interface NetworkFeedFetcherInterface
{
    public function fetch(SocialNetworkProfile $socialNetworkProfile, FetchInfo $fetchInfo): NetworkFeedFetcherInterface;

    public function supports(SocialNetworkProfile $socialNetworkProfile): bool;

    public function supportsNetwork(string $network): bool;

    public function getNetworkIdentifier(): string;
}
