<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedsApi;

use App\Criticalmass\SocialNetwork\FeedsApi\Dto\Network;
use App\Criticalmass\SocialNetwork\Network\NetworkInterface;
use App\Criticalmass\SocialNetwork\NetworkManager\NetworkManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class FeedsNetworkManager implements NetworkManagerInterface
{
    private ?array $networkList = null;

    public function __construct(
        private readonly FeedsApiClientInterface $feedsApiClient,
        private readonly CacheInterface $cache,
    ) {
    }

    public function addNetwork(NetworkInterface $network): static
    {
        return $this;
    }

    public function getNetworkList(): array
    {
        if ($this->networkList === null) {
            $this->networkList = $this->loadNetworks();
        }

        return $this->networkList;
    }

    public function hasNetwork(string $identifier): bool
    {
        return array_key_exists($identifier, $this->getNetworkList());
    }

    public function getNetwork(string $identifier): NetworkInterface
    {
        $list = $this->getNetworkList();

        if (!isset($list[$identifier])) {
            throw new \InvalidArgumentException(sprintf('Network "%s" not found', $identifier));
        }

        return $list[$identifier];
    }

    /** @return array<string, Network> */
    private function loadNetworks(): array
    {
        return $this->cache->get('feeds_api_networks', function (ItemInterface $item): array {
            $item->expiresAfter(3600);

            $networks = $this->feedsApiClient->getNetworks();
            $indexed = [];

            foreach ($networks as $network) {
                $indexed[$network->getIdentifier()] = $network;
            }

            return $indexed;
        });
    }
}
