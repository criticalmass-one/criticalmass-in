<?php

namespace Criticalmass\Bundle\AppBundle\Criticalmass\SocialNetwork\Network;

use Criticalmass\Bundle\AppBundle\Entity\SocialNetworkProfile;

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
