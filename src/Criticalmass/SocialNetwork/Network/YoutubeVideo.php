<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

use App\Entity\SocialNetworkProfile;

class YoutubeVideo extends AbstractNetwork
{
    /** @var string $name */
    protected $name = 'YouTube-Video';

    /** @var int $detectorPriority */
    protected $detectorPriority = -10;

    public function accepts(SocialNetworkProfile $socialNetworkProfile): bool
    {
        $pattern = '/^((?:https?:)?\/\/)?((?:www|m)\.)?((?:youtube\.com|youtu.be))(\/(?:watch+\?v=|embed\/|v\/)?)([\w\-]+)(\S+)?$/';

        preg_match($pattern, $socialNetworkProfile->getIdentifier(), $matches);

        return $matches && is_array($matches) && count($matches) > 1;
    }
}
