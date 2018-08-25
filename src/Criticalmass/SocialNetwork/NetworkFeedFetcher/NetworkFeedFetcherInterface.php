<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\NetworkFeedFetcher;

use App\Entity\SocialNetworkProfile;

interface NetworkFeedFetcherInterface
{
    public function fetch(SocialNetworkProfile $socialNetworkProfile): NetworkFeedFetcherInterface;
}
