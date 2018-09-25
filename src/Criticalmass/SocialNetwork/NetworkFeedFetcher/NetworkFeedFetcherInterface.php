<?php

namespace App\Criticalmass\SocialNetwork\NetworkFeedFetcher;

use App\Entity\SocialNetworkProfile;

interface NetworkFeedFetcherInterface
{
    public function fetch(SocialNetworkProfile $socialNetworkProfile): NetworkFeedFetcherInterface;
}
