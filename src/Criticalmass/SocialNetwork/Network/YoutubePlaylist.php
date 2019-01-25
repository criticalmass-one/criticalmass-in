<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

use App\Entity\SocialNetworkProfile;

class YoutubePlaylist extends AbstractNetwork
{
    protected $name = 'YouTube-Playlist';

    protected $icon = 'fa-youtube';

    protected $backgroundColor = 'rgb(220, 78, 65)';

    protected $textColor = 'white';

    public function accepts(SocialNetworkProfile $socialNetworkProfile): bool
    {
        $pattern = '/^(https?\:\/\/)?(www\.)?(youtube\.com)\/(playlist)\/.+$/';

        preg_match($pattern, $socialNetworkProfile->getIdentifier(), $matches);

        if ($matches && is_array($matches) && count($matches) > 1) {
            return true;
        }

        return false;
    }
}
