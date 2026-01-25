<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

class WhatsappChat extends AbstractNetwork
{
    protected string $name = 'WhatsApp-Chat';

    protected string $icon = 'fab fa-whatsapp';

    protected string $backgroundColor = 'rgb(65, 193, 83)';

    protected string $textColor = 'white';

    public function accepts(string $url): bool
    {
        $pattern = '/^(https?\:\/\/chat\.whatsapp\.com\/)(.+)$/';

        preg_match($pattern, $url, $matches);

        return $matches && is_array($matches) && count($matches) > 1;
    }
}
