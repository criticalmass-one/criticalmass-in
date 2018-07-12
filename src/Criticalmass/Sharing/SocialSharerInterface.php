<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Sharing;

use AppBundle\Criticalmass\Sharing\Network\ShareNetworkInterface;
use AppBundle\Criticalmass\Sharing\ShareableInterface\Shareable;

interface SocialSharerInterface
{
    public function addShareNetwork(ShareNetworkInterface $shareNetwork): SocialSharerInterface;
    public function getNetwork(string $identifier): ShareNetworkInterface;
    public function createUrlForShareable(Shareable $shareable, string $network): string;
}
