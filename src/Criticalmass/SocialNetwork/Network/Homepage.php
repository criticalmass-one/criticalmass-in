<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

class Homepage extends AbstractNetwork
{
    protected string $name = 'Homepage';

    protected string $icon = 'far fa-home';

    protected string $backgroundColor = 'white';

    protected string $textColor = 'black';

    protected int $detectorPriority = -100;

    public function accepts(string $url): bool
    {
        return false !== filter_var($url, FILTER_VALIDATE_URL);
    }
}
