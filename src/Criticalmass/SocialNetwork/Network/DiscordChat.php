<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

class DiscordChat extends AbstractNetwork
{
    /** @var string $name */
    protected $name = 'Discord-Chat';

    /** @var string $icon */
    protected $icon = 'fab fa-discord';

    /** @var string $backgroundColor */
    protected $backgroundColor = 'rgb(114, 137, 218)';

    /** @var string $textColor */
    protected $textColor = 'white';

    public function accepts(string $url): bool
    {
        $pattern = '/^(https?\:\/\/discord)(app\.com\/|\.gg\/)(.+)$/';

        preg_match($pattern, $url, $matches);

        return $matches && is_array($matches) && count($matches) > 1;
    }
}
