<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

class StravaRoute extends AbstractStravaNetwork
{
    protected string $name = 'Strava-Route';

    public function accepts(string $url): bool
    {
        return strpos($url, 'strava.com/routes/') !== false;
    }
}
