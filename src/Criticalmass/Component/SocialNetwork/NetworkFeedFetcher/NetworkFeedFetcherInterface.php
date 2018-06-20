<?php

namespace Criticalmass\Component\SocialNetwork\NetworkFeedFetcher;

use Criticalmass\Bundle\AppBundle\Entity\SocialNetworkProfile;

interface NetworkFeedFetcherInterface
{
    public function fetch(SocialNetworkProfile $socialNetworkProfile): NetworkFeedFetcherInterface;
}
