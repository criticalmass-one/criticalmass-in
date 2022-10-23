<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\NetworkDetector;

use App\Criticalmass\SocialNetwork\Network\NetworkInterface;
use App\Criticalmass\SocialNetwork\NetworkManager\NetworkManager;
use App\Criticalmass\SocialNetwork\NetworkManager\NetworkManagerInterface;

abstract class AbstractNetworkDetector implements NetworkDetectorInterface
{
    /** @var array $networkList */
    protected $networkList = [];

    public function __construct(protected NetworkManagerInterface $networkManager)
    {
        $this->networkList = $networkManager->getNetworkList();

        $this->sortNetworkList();
    }

    protected function sortNetworkList(): NetworkDetector
    {
        usort($this->networkList, fn(NetworkInterface $a, NetworkInterface$b) => $b->getDetectorPriority() <=> $a->getDetectorPriority());

        return $this;
    }
}
