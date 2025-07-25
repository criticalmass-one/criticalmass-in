<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\NetworkDetector;

use App\Criticalmass\SocialNetwork\Network\NetworkInterface;
use App\Criticalmass\SocialNetwork\NetworkManager\NetworkManager;
use App\Criticalmass\SocialNetwork\NetworkManager\NetworkManagerInterface;

class NetworkDetector implements NetworkDetectorInterface
{
    private array $networkList = [];

    public function __construct(private readonly NetworkManagerInterface $networkManager)
    {
        $this->networkList = $this->networkManager->getNetworkList();

        $this->sortNetworkList();
    }

    protected function sortNetworkList(): NetworkDetector
    {
        usort($this->networkList, function(NetworkInterface $a, NetworkInterface$b)
        {
            return $b->getDetectorPriority() <=> $a->getDetectorPriority();
        });

        return $this;
    }

    public function detect(string $url): ?NetworkInterface
    {
        /** @var NetworkInterface $network */
        foreach ($this->networkList as $network) {
            if ($network->accepts($url)) {
                return $network;
            }
        }

        return null;
    }
}
