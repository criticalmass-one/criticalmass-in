<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

abstract class AbstractYoutubeNetwork extends AbstractNetwork
{
    protected string $icon = 'fab fa-youtube';

    protected string $backgroundColor = 'rgb(255, 0, 0)';

    protected string $textColor = 'white';
}
