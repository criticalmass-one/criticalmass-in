<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

class StravaActivity extends AbstractStravaNetwork
{
    protected string $name = 'Strava-Aktivität';

    public function accepts(string $url): bool
    {
        return strpos($url, 'strava.com/activities/') !== false;
    }
}
