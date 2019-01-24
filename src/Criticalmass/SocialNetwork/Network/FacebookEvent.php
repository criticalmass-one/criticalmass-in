<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

use App\Entity\SocialNetworkProfile;

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
