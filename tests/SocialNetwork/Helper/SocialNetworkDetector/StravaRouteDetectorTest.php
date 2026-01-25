<?php declare(strict_types=1);

namespace Tests\SocialNetwork\Helper\SocialNetworkDetector;

class StravaRouteDetectorTest extends AbstractNetworkDetectorTestCase
{
    public function testStravaRoute(): void
    {
        $network = $this->detect('https://www.strava.com/routes/26021459');

        $this->assertEquals('strava_route', $network->getIdentifier());
    }
}
