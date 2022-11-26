<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

class Mastodon extends AbstractNetwork
{
    protected string $name = 'Mastodon';

    protected string $icon = 'fab fa-mastodon';

    protected string $backgroundColor = 'rgb(96, 94, 239)';

    protected string $textColor = 'white';

    public function accepts(string $url): bool
    {
        $pattern = '/@?\b([A-Z0-9._%+-]+)@([A-Z0-9.-]+\.[A-Z]{2,})/';

        preg_match($pattern, $url, $matches);

        return $matches && is_array($matches) && count($matches) > 1;
    }
}
