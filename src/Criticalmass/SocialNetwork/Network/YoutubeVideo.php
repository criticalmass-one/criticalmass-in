<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

use App\Entity\SocialNetworkProfile;

class YoutubeVideo extends AbstractNetwork
{
    protected $name = 'YouTube-Video';

    protected $icon = 'fa-youtube';

    protected $backgroundColor = 'rgb(220, 78, 65)';

    protected $textColor = 'white';

    protected $detectorPriority = -10;

    public function accepts(SocialNetworkProfile $socialNetworkProfile): bool
    {
        $pattern = '/^((?:https?:)?\/\/)?((?:www|m)\.)?((?:youtube\.com|youtu.be))(\/(?:watch+\?v=|embed\/|v\/)?)([\w\-]+)(\S+)?$/';

        preg_match($pattern, $socialNetworkProfile->getIdentifier(), $matches);

        if ($matches && is_array($matches) && count($matches) > 1) {
            return true;
        }

        return false;
    }
}
