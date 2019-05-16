<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\EntityNetworkDetector;

use Caldera\SocialNetworkBundle\Network\NetworkInterface;
use Caldera\SocialNetworkBundle\NetworkManager\NetworkManagerInterface;

abstract class AbstractEntityNetworkDetector implements EntityNetworkDetectorInterface
{
    /** @var NetworkManagerInterface $networkManager */
    protected $networkManager;

    /** @var array $networkList */
    protected $networkList = [];

    public function __construct(NetworkManagerInterface $networkManager)
    {
        $this->networkManager = $networkManager;
        $this->networkList = $networkManager->getNetworkList();

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
}
