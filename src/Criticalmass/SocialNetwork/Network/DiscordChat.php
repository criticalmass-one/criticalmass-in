<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

class DiscordChat extends AbstractNetwork
{
    protected string $name = 'Discord-Chat';

    protected string $icon = 'fab fa-discord';

    protected string $backgroundColor = 'rgb(114, 137, 218)';

    protected string $textColor = 'white';

    public function accepts(string $url): bool
    {
        $pattern = '/^(https?\:\/\/discord)(app\.com\/|\.gg\/)(.+)$/';

        preg_match($pattern, $url, $matches);

        return $matches && is_array($matches) && count($matches) > 1;
    }
}
