<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\NetworkDetector;

use App\Criticalmass\SocialNetwork\Network\NetworkInterface;

class NetworkDetector extends AbstractNetworkDetector
{
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
