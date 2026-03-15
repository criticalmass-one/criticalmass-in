<?php declare(strict_types=1);

namespace Tests\SocialNetwork\Helper\SocialNetworkDetector;

class FacebookEventDetectorTest extends AbstractNetworkDetectorTestCase
{
    public function testFacebookGroup(): void
    {
        $network = $this->detect('https://www.facebook.com/events/1153532054978391/');

        $this->assertEquals('facebook_event', $network->getIdentifier());
    }
}
