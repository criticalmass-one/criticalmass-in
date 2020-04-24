<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedFetcher;

use App\Entity\SocialNetworkProfile;
use App\Criticalmass\SocialNetwork\NetworkFeedFetcher\NetworkFeedFetcherInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

abstract class AbstractFeedFetcher
{
    /** @var array $networkFetcherList */
    protected $networkFetcherList = [];

    /** @var array $feedFetcher */
    protected $fetchableNetworkList = [];

    /** @var RegistryInterface $doctrine */
    protected $doctrine;

    protected $feedItemList = [];

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function addNetworkFeedFetcher(NetworkFeedFetcherInterface $networkFeedFetcher): AbstractFeedFetcher
    {
        $this->networkFetcherList[] = $networkFeedFetcher;

        return $this;
    }

    public function addFetchableNetwork(string $network): AbstractFeedFetcher
    {
        $this->fetchableNetworkList[] = $network;

        return $this;
    }

    public function getNetworkFetcherList(): array
    {
        return $this->networkFetcherList;
    }

    protected function getSocialNetworkProfiles(): array
    {
        return $this->doctrine->getRepository(SocialNetworkProfile::class)->findAll();
    }

    public function getFeedItemList(): array
    {
        return $this->feedItemList;
    }
}
