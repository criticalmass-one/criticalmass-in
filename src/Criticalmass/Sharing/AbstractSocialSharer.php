<?php declare(strict_types=1);

namespace App\Criticalmass\Sharing;

use App\Criticalmass\Sharing\Network\ShareNetworkInterface;

abstract class AbstractSocialSharer implements SocialSharerInterface
{
    protected $shareNetworkList = [];

    public function addShareNetwork(ShareNetworkInterface $shareNetwork): SocialSharerInterface
    {
        $this->shareNetworkList[$shareNetwork->getIdentifier()] = $shareNetwork;

        return $this;
    }

    public function getNetwork(string $identifier): ShareNetworkInterface
    {
        if (array_key_exists($identifier, $this->shareNetworkList)) {
            return $this->shareNetworkList[$identifier];
        }

        throw new \Exception();
    }
}
