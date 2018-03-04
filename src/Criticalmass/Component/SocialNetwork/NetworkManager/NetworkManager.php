<?php

namespace Criticalmass\Component\SocialNetwork\NetworkManager;

use Criticalmass\Component\SocialNetwork\Network\NetworkInterface;

class NetworkManager
{
    protected $networkList = [];

    public function addNetwork(NetworkInterface $network): NetworkManager
    {
        $this->networkList[] = $network;

        return $this;
    }

    public function getNetworkList(): array
    {
        return $this->networkList;
    }
}
