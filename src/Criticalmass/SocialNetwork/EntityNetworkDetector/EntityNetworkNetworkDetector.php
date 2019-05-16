<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\EntityNetworkDetector;

use App\Entity\SocialNetworkProfile;
use Caldera\SocialNetworkBundle\Network\NetworkInterface;

class EntityNetworkNetworkDetector extends AbstractEntityNetworkDetector
{
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
