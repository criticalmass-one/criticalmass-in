<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

use App\Entity\SocialNetworkProfile;

class InstagramProfile extends AbstractInstagramNetwork
{
    protected $name = 'Instagram-Profil';

    public function accepts(SocialNetworkProfile $socialNetworkProfile): bool
    {
        $pattern = '/^(https?\:\/\/)?(www\.)?(instagram\.com)\/.+$/';

        preg_match($pattern, $socialNetworkProfile->getIdentifier(), $matches);

        if ($matches && is_array($matches) && count($matches) > 1) {
            return true;
        }

        return false;
    }
}
