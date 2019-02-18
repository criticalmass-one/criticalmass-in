<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

use App\Entity\SocialNetworkProfile;

class YoutubeUser extends AbstractNetwork
{
    /** @var string $name */
    protected $name = 'YouTube-Konto';

    public function accepts(SocialNetworkProfile $socialNetworkProfile): bool
    {
        $pattern = '/^(https?\:\/\/)?(www\.)?(youtube\.com)\/(user)\/.+$/';

        preg_match($pattern, $socialNetworkProfile->getIdentifier(), $matches);

        return $matches && is_array($matches) && count($matches) > 1;
    }
}
