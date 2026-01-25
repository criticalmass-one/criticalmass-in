<?php declare(strict_types=1);

namespace Tests\SocialNetwork\Helper\SocialNetworkDetector;

class StravaClubDetectorTest extends AbstractNetworkDetectorTestCase
{
    public function testStravaClub(): void
    {
        $network = $this->detect('https://www.strava.com/clubs/criticalmass_rennradteam');

        $this->assertEquals('strava_club', $network->getIdentifier());

        $network = $this->detect('https://www.strava.com/clubs/criticalmassstuttgart');

        $this->assertEquals('strava_club', $network->getIdentifier());
    }
}
