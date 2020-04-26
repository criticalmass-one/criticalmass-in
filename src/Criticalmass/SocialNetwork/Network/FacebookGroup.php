<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

class FacebookGroup extends AbstractFacebookNetwork
{
    protected string $name = 'Facebook-Gruppe';

    public function accepts(string $url): bool
    {
        if (!parent::accepts($url)) {
            return false;
        }
        
        return strpos($url, 'facebook.com/groups/') !== false;
    }
}
