<?php

namespace AppBundle\Criticalmass\SocialNetwork\Network;

use AppBundle\Entity\SocialNetworkProfile;

class FacebookGroup extends AbstractFacebookNetwork
{
    protected $name = 'facebook-Gruppe';

    public function accepts(SocialNetworkProfile $socialNetworkProfile): bool
    {
        if (!parent::accepts($socialNetworkProfile)) {
            return false;
        }

        return strpos($socialNetworkProfile->getIdentifier(), 'facebook.com/groups/') !== false;
    }
}
