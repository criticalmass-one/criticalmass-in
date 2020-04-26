<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

class StravaActivity extends AbstractStravaNetwork
{
    /** @var string $name */
    protected $name = 'Strava-Aktivität';

    public function accepts(string $url): bool
    {
        return strpos($url, 'strava.com/activities/') !== false;
    }
}
