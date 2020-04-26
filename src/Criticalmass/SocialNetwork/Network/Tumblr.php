<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

class Tumblr extends AbstractNetwork
{
    protected string $name = 'Tumblr';

    protected string $icon = 'fab fa-tumblr';

    protected string $backgroundColor = 'rgb(0, 0, 0)';

    protected string $textColor = 'white';

    public function accepts(string $url): bool
    {
        $pattern = '/^(https?\:\/\/)((www\.)?)([a-zA-Z0-9]*)\.(tumblr\.com)(\/?)$/';

        preg_match($pattern, $url, $matches);

        return $matches && is_array($matches) && count($matches) > 1;
    }
}
