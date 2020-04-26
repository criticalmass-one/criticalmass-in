<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

abstract class AbstractStravaNetwork extends AbstractNetwork
{
    /** @var string $icon */
    protected $icon = 'fab fa-strava';

    /** @var string $backgroundColor */
    protected $backgroundColor = 'rgb(252, 82, 0)';

    /** @var string $textColor */
    protected $textColor = 'white';
}
