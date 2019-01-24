<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

use App\Entity\SocialNetworkProfile;
use Facebook\Exceptions\FacebookResponseException;

class FacebookProfile extends AbstractFacebookNetwork
{
    protected $name = 'facebook-Profil';

    public function accepts(SocialNetworkProfile $socialNetworkProfile): bool
    {
        if (!parent::accepts($socialNetworkProfile)) {
            return false;
        }

        $profileName = $this->getProfileFromUrl($socialNetworkProfile);

        // Sorry, this is so ugly, please provide a better solution and do not try this at home!
        try {
            $endpoint = sprintf('/%s', $profileName);
            $result = $this->facebookApi->query($endpoint);
        } catch (FacebookResponseException $exception) {
            if (strpos($exception->getMessage(), '(#803) Cannot query users by their username') !== false) {
                return true;
            }
        }

        return false;
    }
}
