<?php

namespace AppBundle\Criticalmass\SocialNetwork\Network;

use AppBundle\Entity\SocialNetworkProfile;

class FacebookEvent extends AbstractFacebookNetwork
{
    protected $name = 'facebook-Event';

    public function accepts(SocialNetworkProfile $socialNetworkProfile): bool
    {
        if (!parent::accepts($socialNetworkProfile)) {
            return false;
        }

        return strpos($socialNetworkProfile->getIdentifier(), 'facebook.com/events/') !== false;
    }
}
