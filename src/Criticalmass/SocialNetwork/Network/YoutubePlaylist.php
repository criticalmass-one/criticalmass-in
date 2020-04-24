<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

class YoutubePlaylist extends AbstractYoutubeNetwork
{
    /** @var string $name */
    protected $name = 'YouTube-Playlist';

    public function accepts(string $url): bool
    {
        $pattern = '/^(https?\:\/\/)?(www\.)?(youtube\.com)\/(playlist)\?.+$/';

        preg_match($pattern, $url, $matches);

        return $matches && is_array($matches) && count($matches) > 1;
    }
}
