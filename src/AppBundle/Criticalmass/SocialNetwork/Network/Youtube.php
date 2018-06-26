<?php

namespace AppBundle\Criticalmass\SocialNetwork\Network;

use AppBundle\Entity\SocialNetworkProfile;

class Youtube extends AbstractNetwork
{
    protected $name = 'YouTube';

    protected $icon = 'fa-youtube';

    protected $backgroundColor = 'rgb(220, 78, 65)';

    protected $textColor = 'white';

    /** TODO add regex here */
    public function accepts(SocialNetworkProfile $socialNetworkProfile): bool
    {
        return strpos($socialNetworkProfile->getIdentifier(), '.youtube.com') !== false;
    }
}
