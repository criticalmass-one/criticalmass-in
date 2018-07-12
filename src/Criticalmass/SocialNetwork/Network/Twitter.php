<?php

namespace App\Criticalmass\SocialNetwork\Network;

use App\Entity\SocialNetworkProfile;

class Twitter extends AbstractNetwork
{
    protected $name = 'twitter';

    protected $icon = 'fa-twitter';

    protected $backgroundColor = 'rgb(85, 172, 238)';

    protected $textColor = 'white';

    public function accepts(SocialNetworkProfile $socialNetworkProfile): bool
    {
        $pattern = '/http(?:s)?:\/\/(?:www\.)?twitter\.com\/([a-zA-Z0-9_]+)/';

        preg_match($pattern, $socialNetworkProfile->getIdentifier(), $matches);

        if ($matches && is_array($matches) && 2 === count($matches)) {
            return true;
        }

        return false;
    }
}
