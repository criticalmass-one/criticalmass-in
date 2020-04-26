<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

class FacebookProfile extends AbstractFacebookNetwork
{
    protected string $name = 'facebook-Profil';

    public function accepts(string $url): bool
    {
        if (!parent::accepts($url)) {
            return false;
        }

        return strpos($url, 'facebook.com/profile.php?id=') !== false;
    }
}
