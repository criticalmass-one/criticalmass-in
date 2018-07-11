<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Sharing;

use AppBundle\Criticalmass\Sharing\Network\ShareNetworkInterface;
use AppBundle\Criticalmass\Sharing\ShareableInterface\Shareable;

class SocialSharer
{
    protected $shareNetworkList = [];

    public function addShareNetwork(ShareNetworkInterface $shareNetwork): SocialSharer
    {
        $this->shareNetworkList[$shareNetwork->getIdentifier()] = $shareNetwork;

        return $this;
    }

    public function createUrlForShareable(Shareable $shareable, string $network): string
    {
        return $this->shareNetworkList[$network]->createUrlForShareable($shareable);
    }

    public function getNetwork(string $identifier): ShareNetworkInterface
    {
        if (array_key_exists($identifier, $this->shareNetworkList)) {
            return $this->shareNetworkList[$identifier];
        }

        throw new \Exception();
    }
}
