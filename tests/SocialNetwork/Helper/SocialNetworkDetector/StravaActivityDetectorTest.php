<?php declare(strict_types=1);

namespace Tests\SocialNetwork\Helper\SocialNetworkDetector;

class StravaActivityDetectorTest extends AbstractNetworkDetectorTestCase
{
    public function testStravaActivity(): void
    {
        $network = $this->detect('https://www.strava.com/activities/3351499309');

        $this->assertEquals('strava_activity', $network->getIdentifier());

        $network = $this->detect('https://www.strava.com/activities/3351499309/');

        $this->assertEquals('strava_activity', $network->getIdentifier());
    }
}
