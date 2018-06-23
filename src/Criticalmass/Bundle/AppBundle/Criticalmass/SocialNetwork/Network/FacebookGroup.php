<?php

namespace Criticalmass\Component\SocialNetwork\Network;

use Criticalmass\Bundle\AppBundle\Entity\SocialNetworkProfile;

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
