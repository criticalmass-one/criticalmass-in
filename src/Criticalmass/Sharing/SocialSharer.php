<?php declare(strict_types=1);

namespace App\Criticalmass\Sharing;

use App\Criticalmass\Sharing\ShareableInterface\Shareable;

class SocialSharer extends AbstractSocialSharer
{
    public function createUrlForShareable(Shareable $shareable, string $network): string
    {
        return 'foo';
        return $this->shareNetworkList[$network]->createUrlForShareable($shareable);
    }
}
