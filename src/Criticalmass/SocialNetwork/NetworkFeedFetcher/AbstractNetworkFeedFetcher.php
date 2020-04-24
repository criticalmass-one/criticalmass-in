<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\NetworkFeedFetcher;

use App\Entity\SocialNetworkProfile;
use Psr\Log\LoggerInterface;

abstract class AbstractNetworkFeedFetcher implements NetworkFeedFetcherInterface
{
    /** @var array $feedItemList */
    protected $feedItemList = [];

    /** @var LoggerInterface $logger */
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getFeedItemList(): array
    {
        return $this->feedItemList;
    }

    public function supports(SocialNetworkProfile $socialNetworkProfile): bool
    {
        return $this->supportsNetwork($socialNetworkProfile->getNetwork());
    }

    public function supportsNetwork(string $network): bool
    {
        return $this->getNetworkIdentifier() === $network;
    }

    public function getNetworkIdentifier(): string
    {
        $fqcn = get_class($this);
        $parts = explode('\\', $fqcn);
        $classname = array_pop($parts);
        $feedFetcherNetwork = str_replace('FeedFetcher', '', $classname);

        return strtolower($feedFetcherNetwork);
    }
}
