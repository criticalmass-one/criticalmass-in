<?php

namespace AppBundle\Criticalmass\SocialNetwork\Network;

use AppBundle\Entity\SocialNetworkProfile;

class Tumblr extends AbstractNetwork
{
    protected $name = 'Tumblr';

    protected $icon = 'fa-tumblr';

    protected $backgroundColor = 'rgb(220, 78, 65)';

    protected $textColor = 'white';

    /** TODO add regex here */
    public function accepts(SocialNetworkProfile $socialNetworkProfile): bool
    {
        return strpos($socialNetworkProfile->getIdentifier(), '.tumblr.com') !== false;
    }
}
