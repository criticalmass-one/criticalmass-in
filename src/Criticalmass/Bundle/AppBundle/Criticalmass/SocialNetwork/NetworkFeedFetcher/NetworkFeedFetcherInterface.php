<?php

namespace Criticalmass\Bundle\AppBundle\Criticalmass\SocialNetwork\NetworkFeedFetcher;

use Criticalmass\Bundle\AppBundle\Entity\SocialNetworkProfile;

interface NetworkFeedFetcherInterface
{
    public function fetch(SocialNetworkProfile $socialNetworkProfile): NetworkFeedFetcherInterface;
}
