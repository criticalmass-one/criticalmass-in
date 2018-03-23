<?php

namespace Criticalmass\Component\SocialNetwork\Network;

use Criticalmass\Bundle\AppBundle\Entity\SocialNetworkProfile;

abstract class AbstractFacebookNetwork extends AbstractNetwork
{
    protected $name = 'facebook';

    protected $icon = 'fa-facebook';

    protected $backgroundColor = 'rgb(59, 90, 153)';

    protected $textColor = 'white';

    public function accepts(SocialNetworkProfile $socialNetworkProfile): bool
    {
        $pattern = '/(?:(?:http|https):\/\/)?(?:www.)?facebook.com\/(?:(?:\w)*#!\/)?(?:pages\/)?(?:[?\w\-]*\/)?(?:profile.php\?id=(?=\d.*))?([\w\-]*)?/';

        preg_match($pattern, $socialNetworkProfile->getIdentifier(), $matches);

        return $matches && is_array($matches) && 2 === count($matches);
    }
}
