<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

class StravaClub extends AbstractStravaNetwork
{
    protected string $name = 'Strava-Club';

    public function accepts(string $url): bool
    {
        return str_contains($url, 'strava.com/clubs/');
    }
}
