<?php declare(strict_types=1);

namespace Tests\SocialNetworkDetector;

class StravaActivityDetectorTest extends AbstractNetworkDetectorTest
{
    public function testStravaActivity(): void
    {
        $network = $this->detect('https://www.strava.com/activities/3351499309');

        $this->assertEquals('strava_activity', $network->getIdentifier());

        $network = $this->detect('https://www.strava.com/activities/3355136046?share_sig=7PPQLS101587897695&utm_medium=social&utm_source=android_share');

        $this->assertEquals('strava_activity', $network->getIdentifier());
    }
}
