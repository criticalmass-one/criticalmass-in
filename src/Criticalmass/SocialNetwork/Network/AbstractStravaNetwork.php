<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

abstract class AbstractStravaNetwork extends AbstractNetwork
{
    protected string $icon = 'fab fa-strava';

    protected string $backgroundColor = 'rgb(252, 82, 0)';

    protected string $textColor = 'white';
}
