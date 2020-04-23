<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

class Tumblr extends AbstractNetwork
{
    /** @var string $name */
    protected $name = 'Tumblr';

    /** @var string $icon */
    protected $icon = 'fab fa-tumblr';

    /** @var string $backgroundColor */
    protected $backgroundColor = 'rgb(0, 0, 0)';

    /** @var string $textColor */
    protected $textColor = 'white';

    public function accepts(string $url): bool
    {
        $pattern = '/^(https?\:\/\/)((www\.)?)([a-zA-Z0-9]*)\.(tumblr\.com)(\/?)$/';

        preg_match($pattern, $url, $matches);

        return $matches && is_array($matches) && count($matches) > 1;
    }
}
