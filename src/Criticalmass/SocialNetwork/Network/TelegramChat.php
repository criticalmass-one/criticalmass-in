<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

class TelegramChat extends AbstractNetwork
{
    /** @var string $name */
    protected $name = 'Telegram-Chat';

    /** @var string $icon */
    protected $icon = 'fab fa-telegram-plane';

    /** @var string $backgroundColor */
    protected $backgroundColor = 'rgb(40, 159, 217)';

    /** @var string $textColor */
    protected $textColor = 'white';

    public function accepts(string $url): bool
    {
        $pattern = '/^(https?\:\/\/t\.me\/)(.+)$/';

        preg_match($pattern, $url, $matches);

        return $matches && is_array($matches) && count($matches) > 1;
    }
}
