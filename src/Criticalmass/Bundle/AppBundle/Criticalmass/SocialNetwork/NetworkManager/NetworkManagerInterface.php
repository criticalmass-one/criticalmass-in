<?php

namespace Criticalmass\Component\SocialNetwork\NetworkManager;

use Criticalmass\Component\SocialNetwork\Network\NetworkInterface;

interface NetworkManagerInterface
{
    public function addNetwork(NetworkInterface $network): NetworkManager;
    public function getNetworkList(): array;
    public function hasNetwork(string $identifier): bool;
    public function getNetwork(string $identifier): NetworkInterface;
}
