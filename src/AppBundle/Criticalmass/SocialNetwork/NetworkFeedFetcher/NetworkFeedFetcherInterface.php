<?php

namespace AppBundle\Criticalmass\SocialNetwork\NetworkFeedFetcher;

use AppBundle\Entity\SocialNetworkProfile;

interface NetworkFeedFetcherInterface
{
    public function fetch(SocialNetworkProfile $socialNetworkProfile): NetworkFeedFetcherInterface;
}
