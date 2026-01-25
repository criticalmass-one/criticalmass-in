<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

class TelegramChat extends AbstractNetwork
{
    protected string $name = 'Telegram-Chat';

    protected string $icon = 'fab fa-telegram-plane';

    protected string $backgroundColor = 'rgb(40, 159, 217)';

    protected string $textColor = 'white';

    public function accepts(string $url): bool
    {
        $pattern = '/^(https?\:\/\/t\.me\/)(.+)$/';

        preg_match($pattern, $url, $matches);

        return $matches && is_array($matches) && count($matches) > 1;
    }
}
