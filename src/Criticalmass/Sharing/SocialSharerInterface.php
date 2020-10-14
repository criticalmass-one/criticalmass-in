<?php declare(strict_types=1);

namespace App\Criticalmass\Sharing;

use App\Criticalmass\Sharing\Network\ShareNetworkInterface;
use App\Criticalmass\Sharing\ShareableInterface\Shareable;

interface SocialSharerInterface
{
    public function addShareNetwork(ShareNetworkInterface $shareNetwork): SocialSharerInterface;
    public function getNetwork(string $identifier): ShareNetworkInterface;
    public function createUrlForShareable(Shareable $shareable, string $network): string;
}
