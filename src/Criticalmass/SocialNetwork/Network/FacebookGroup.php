<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

class FacebookGroup extends AbstractFacebookNetwork
{
    /** @var string $name */
    protected $name = 'facebook-Gruppe';

    public function accepts(string $url): bool
    {
        if (!parent::accepts($url)) {
            return false;
        }

        return strpos($url, 'facebook.com/groups/') !== false;
    }
}
