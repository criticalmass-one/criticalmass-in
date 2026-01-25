<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\EntityNetworkDetector;

use App\Criticalmass\SocialNetwork\Network\NetworkInterface;
use App\Criticalmass\SocialNetwork\NetworkManager\NetworkManagerInterface;
use App\Entity\SocialNetworkProfile;

class EntityNetworkDetector implements EntityNetworkDetectorInterface
{
    private array $networkList = [];

    public function __construct(private readonly NetworkManagerInterface $networkManager)
    {
        $this->networkList = $this->networkManager->getNetworkList();

        $this->sortNetworkList();
    }

    protected function sortNetworkList(): self
    {
        usort($this->networkList, function(NetworkInterface $a, NetworkInterface $b)
        {
            return $b->getDetectorPriority() <=> $a->getDetectorPriority();
        });

        return $this;
    }

    public function detect(SocialNetworkProfile $socialNetworkProfile): ?NetworkInterface
    {
        if (!$socialNetworkProfile->getIdentifier()) {
            return null;
        }

        /** @var NetworkInterface $network */
        foreach ($this->networkList as $network) {
            if ($network->accepts($socialNetworkProfile->getIdentifier())) {
                return $network;
            }
        }

        return null;
    }
}
