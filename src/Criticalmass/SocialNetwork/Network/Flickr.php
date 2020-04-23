<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

class Flickr extends AbstractNetwork
{
    /** @var string $name */
    protected $name = 'flickr';

    /** @var string $icon */
    protected $icon = 'fab fa-flickr';

    /** @var string $backgroundColor */
    protected $backgroundColor = 'rgb(12, 101, 211)';

    /** @var string $textColor */
    protected $textColor = 'white';

    public function accepts(string $url): bool
    {
        $pattern = '/^(https?\:\/\/)?(www\.)?(flickr\.com)\/(photos)\/.+$/';

        preg_match($pattern, $url, $matches);

        return $matches && is_array($matches) && count($matches) > 1;
    }
}
