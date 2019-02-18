<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\NetworkDetector;

use App\Entity\SocialNetworkProfile;
use App\Criticalmass\SocialNetwork\Network\NetworkInterface;

class NetworkDetector extends AbstractNetworkDetector
{
    public function detect(SocialNetworkProfile $socialNetworkProfile): ?NetworkInterface
    {
        if (!$socialNetworkProfile->getIdentifier()) {
            return null;
        }

        /** @var NetworkInterface $network */
        foreach ($this->networkList as $network) {
            if ($network->accepts($socialNetworkProfile)) {
                return $network;
            }
        }

        return null;
    }
}
