<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\NetworkFeedFetcher;

use App\Entity\SocialNetworkProfile;

abstract class AbstractNetworkFeedFetcher implements NetworkFeedFetcherInterface
{
    /** @var array $feedItemList */
    protected $feedItemList = [];

    public function getFeedItemList(): array
    {
        return $this->feedItemList;
    }

    public function supports(SocialNetworkProfile $socialNetworkProfile): bool
    {
        $fqcn = get_class($this);
        $parts = explode('\\', $fqcn);
        $classname = array_pop($parts);
        $network = str_replace('FeedFetcher', '', $classname);

        return strtolower($network) === $socialNetworkProfile->getNetwork();
    }
}
