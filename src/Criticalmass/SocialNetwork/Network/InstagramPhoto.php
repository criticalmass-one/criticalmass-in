<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

use App\Entity\SocialNetworkProfile;

class InstagramPhoto extends AbstractInstagramNetwork
{
    protected $name = 'Instagram-Foto';

    public function accepts(SocialNetworkProfile $socialNetworkProfile): bool
    {
        $pattern = '/^(https?\:\/\/)?(www\.)?(instagram\.com)\/p\/.+$/';

        preg_match($pattern, $socialNetworkProfile->getIdentifier(), $matches);

        if ($matches && is_array($matches) && count($matches) > 1) {
            return true;
        }

        return false;
    }
}
