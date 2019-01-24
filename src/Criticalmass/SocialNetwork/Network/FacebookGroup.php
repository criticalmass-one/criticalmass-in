<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

use App\Entity\SocialNetworkProfile;

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
