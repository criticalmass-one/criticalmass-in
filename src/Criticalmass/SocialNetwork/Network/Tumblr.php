<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

use App\Entity\SocialNetworkProfile;

class Tumblr extends AbstractNetwork
{
    protected $name = 'Tumblr';

    protected $icon = 'fa-tumblr';

    protected $backgroundColor = 'rgb(220, 78, 65)';

    protected $textColor = 'white';

    /** TODO add regex here */
    public function accepts(SocialNetworkProfile $socialNetworkProfile): bool
    {
        return strpos($socialNetworkProfile->getIdentifier(), '.tumblr.com') !== false;
    }
}
