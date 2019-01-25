<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

use App\Entity\SocialNetworkProfile;

class Youtube extends AbstractNetwork
{
    protected $name = 'YouTube';

    protected $icon = 'fa-youtube';

    protected $backgroundColor = 'rgb(220, 78, 65)';

    protected $textColor = 'white';

    public function accepts(SocialNetworkProfile $socialNetworkProfile): bool
    {
        // ^(https?\:\/\/)?(www\.)?(flickr\.com)\/(photos)\/.+$
        $pattern = '/^(https?\:\/\/)?(www\.)?(youtube\.com)\/(channel)\/.+$/';

        preg_match($pattern, $socialNetworkProfile->getIdentifier(), $matches);

        if ($matches && is_array($matches) && count($matches) > 1) {
            return true;
        }

        return false;
    }
}
