<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

class FacebookPage extends AbstractFacebookNetwork
{
    /** @var string $name */
    protected $name = 'facebook-Seite';

    public function accepts(string $url): bool
    {
        if (!parent::accepts($url)) {
            return false;
        }

     //   $profileName = $this->getProfileFromUrl($url);

        return false;

    }
}