<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

class Flickr extends AbstractNetwork
{
    protected string $name = 'flickr';

    protected string $icon = 'fab fa-flickr';

    protected string $backgroundColor = 'rgb(12, 101, 211)';

    protected string $textColor = 'white';

    public function accepts(string $url): bool
    {
        $pattern = '/^(https?\:\/\/)?(www\.)?(flickr\.com)\/(photos)\/.+$/';

        preg_match($pattern, $url, $matches);

        return $matches && is_array($matches) && count($matches) > 1;
    }
}
