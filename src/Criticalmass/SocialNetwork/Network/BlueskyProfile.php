<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

class BlueskyProfile extends AbstractNetwork
{
    protected string $name = 'Bluesky';

    protected string $icon = 'fa-brands fa-bluesky';

    protected string $backgroundColor = '#0276FF';

    protected string $textColor = 'white';

    public function accepts(string $url): bool
    {
        $patterns = [
            '/^https?:\/\/bsky\.app\/profile\/([a-z0-9.-]+\.[a-z]{2,})\/?$/i',
            '/^([a-z0-9.-]+\.[a-z]{2,})$/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, trim($url))) {
                return true;
            }
        }

        return false;
    }
}
