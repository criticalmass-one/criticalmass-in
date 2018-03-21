<?php
/**
 * Created by PhpStorm.
 * User: maltehuebner
 * Date: 21.03.18
 * Time: 10:29
 */

namespace Criticalmass\Component\SocialNetwork\NetworkDetector;


use Criticalmass\Bundle\AppBundle\Entity\SocialNetworkProfile;
use Criticalmass\Component\SocialNetwork\Network\NetworkInterface;
use Criticalmass\Component\SocialNetwork\NetworkManager\NetworkManager;

class NetworkDetector
{
    /** @var NetworkManager $networkManager */
    protected $networkManager;

    /**
     * @var array $networkList
     */
    protected $networkList = [];

    public function __construct(NetworkManager $networkManager)
    {
        $this->networkManager = $networkManager;
        $this->networkList = $networkManager->getNetworkList();
    }

    public function detect(SocialNetworkProfile $socialNetworkProfile): ?NetworkInterface
    {
        foreach ($this->networkList as $network) {
            if ($network->accepts($socialNetworkProfile)) {
                return $network;
            }
        }

        return null;
    }
}
