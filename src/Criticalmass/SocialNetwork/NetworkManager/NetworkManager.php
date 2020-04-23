<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\NetworkManager;

use App\Criticalmass\Socialnetwork\Network\NetworkInterface;

class NetworkManager implements NetworkManagerInterface
{
    /** @var array $networkList */
    protected $networkList = [];

    public function addNetwork(NetworkInterface $network): NetworkManager
    {
        $this->networkList[$network->getIdentifier()] = $network;

        return $this;
    }

    public function getNetworkList(): array
    {
        return $this->networkList;
    }

    public function hasNetwork(string $identifier): bool
    {
        return array_key_exists($identifier, $this->networkList);
    }

    public function getNetwork(string $identifier): NetworkInterface
    {
        return $this->networkList[$identifier];
    }
}
