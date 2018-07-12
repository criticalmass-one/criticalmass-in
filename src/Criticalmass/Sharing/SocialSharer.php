<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Sharing;

use AppBundle\Criticalmass\Sharing\ShareableInterface\Shareable;

class SocialSharer extends AbstractSocialSharer
{
    public function createUrlForShareable(Shareable $shareable, string $network): string
    {
        return $this->shareNetworkList[$network]->createUrlForShareable($shareable);
    }
}
