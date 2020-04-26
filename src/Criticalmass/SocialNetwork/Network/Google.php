<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

class Google extends AbstractNetwork
{
    protected string $name = 'Google+';

    protected string $icon = 'fab fa-google-plus-g';

    protected string $backgroundColor = 'rgb(234, 66, 53)';

    protected string $textColor = 'white';

    public function accepts(string $url): bool
    {
        $pattern = '/^(https?\:\/\/)((www\.)?)(plus\.google\.com)\/\+([a-zA-Z0-9-]+)(\/?)$/';

        preg_match($pattern, $url, $matches);

        return $matches && is_array($matches) && count($matches) > 1;
    }
}
