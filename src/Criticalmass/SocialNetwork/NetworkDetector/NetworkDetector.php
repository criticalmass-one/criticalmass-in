<?php

namespace App\Criticalmass\SocialNetwork\NetworkDetector;

use App\Entity\SocialNetworkProfile;
use App\Criticalmass\SocialNetwork\Network\NetworkInterface;
use App\Criticalmass\SocialNetwork\NetworkManager\NetworkManager;

class NetworkDetector
{
    /** @var NetworkManager $networkManager */
    protected $networkManager;

    /** @var array $networkList */
    protected $networkList = [];

    public function __construct(NetworkManager $networkManager)
    {
        $this->networkManager = $networkManager;
        $this->networkList = $networkManager->getNetworkList();

        $this->sortNetworkList();
    }

    public function detect(SocialNetworkProfile $socialNetworkProfile): ?NetworkInterface
    {
        /** @var NetworkInterface $network */
        foreach ($this->networkList as $network) {
            echo $network->getName();
            if ($network->accepts($socialNetworkProfile)) {
                return $network;
            }
        }

        return null;
    }

    protected function sortNetworkList(): NetworkDetector
    {
        usort($this->networkList, function(NetworkInterface $a, NetworkInterface$b)
        {
            return $b->getDetectorPriority() <=> $a->getDetectorPriority();
        });

        return $this;
    }
}
