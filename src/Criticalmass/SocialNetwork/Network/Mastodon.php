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
        $pattern = '/@?([a-z0-9._%+-]+)@([a-z0-9.-]+\.[a-z]{2,})/i';

        return preg_match($pattern, $url) === 1;
    }
}
