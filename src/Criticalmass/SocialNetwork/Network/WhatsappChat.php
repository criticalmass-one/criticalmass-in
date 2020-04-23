<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

class WhatsappChat extends AbstractNetwork
{
    /** @var string $name */
    protected $name = 'WhatsApp-Chat';

    /** @var string $icon */
    protected $icon = 'fab fa-whatsapp';

    /** @var string $backgroundColor */
    protected $backgroundColor = 'rgb(65, 193, 83)';

    /** @var string $textColor */
    protected $textColor = 'white';

    public function accepts(string $url): bool
    {
        $pattern = '/^(https?\:\/\/chat\.whatsapp\.com\/)(.+)$/';

        preg_match($pattern, $url, $matches);

        return $matches && is_array($matches) && count($matches) > 1;
    }
}
