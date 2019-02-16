<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

abstract class AbstractYoutubeNetwork extends AbstractNetwork
{
    /** @var string $icon */
    protected $icon = 'fa-youtube';

    /** @var string $backgroundColor */
    protected $backgroundColor = 'rgb(255, 0, 0)';

    /** @var string $textColor */
    protected $textColor = 'white';
}
