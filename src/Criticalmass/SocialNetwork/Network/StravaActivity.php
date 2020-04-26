<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

class StravaActivity extends AbstractNetwork
{
    /** @var string $name */
    protected $name = 'Strava-Aktivität';

    /** @var string $icon */
    protected $icon = 'fab fa-strava';

    /** @var string $backgroundColor */
    protected $backgroundColor = 'rgb(252, 82, 0)';

    /** @var string $textColor */
    protected $textColor = 'white';

    public function accepts(string $url): bool
    {
        return strpos($url, 'strava.com/activities/') !== false;
    }
}
