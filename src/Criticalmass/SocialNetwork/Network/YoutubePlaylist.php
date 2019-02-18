<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

use App\Entity\SocialNetworkProfile;

class YoutubePlaylist extends AbstractNetwork
{
    /** @var string $name */
    protected $name = 'YouTube-Playlist';

    public function accepts(SocialNetworkProfile $socialNetworkProfile): bool
    {
        $pattern = '/^(https?\:\/\/)?(www\.)?(youtube\.com)\/(playlist)\?.+$/';

        preg_match($pattern, $socialNetworkProfile->getIdentifier(), $matches);

        return $matches && is_array($matches) && count($matches) > 1;
    }
}
