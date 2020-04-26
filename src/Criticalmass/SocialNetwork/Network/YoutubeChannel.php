<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

class YoutubeChannel extends AbstractYoutubeNetwork
{
    protected string $name = 'YouTube';

    public function accepts(string $url): bool
    {
        $pattern = '/^(https?\:\/\/)?(www\.)?(youtube\.com)\/(channel)\/.+$/';

        preg_match($pattern, $url, $matches);

        return $matches && is_array($matches) && count($matches) > 1;
    }
}
