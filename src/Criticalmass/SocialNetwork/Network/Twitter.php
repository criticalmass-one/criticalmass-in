<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

class Twitter extends AbstractNetwork
{
    protected string $name = 'twitter';

    protected string $icon = 'fab fa-twitter';

    protected string $backgroundColor = 'rgb(29, 161, 242)';

    protected string $textColor = 'white';

    public function accepts(string $url): bool
    {
        $pattern = '/http(?:s)?:\/\/(?:www\.)?twitter\.com\/([a-zA-Z0-9_]+)/';

        preg_match($pattern, $url, $matches);

        return $matches && is_array($matches) && 2 === count($matches);
    }
}
