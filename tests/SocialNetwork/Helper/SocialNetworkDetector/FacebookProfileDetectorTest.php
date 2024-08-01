<?php declare(strict_types=1);

namespace Tests\SocialNetworkDetector;

class FacebookProfileDetectorTest extends AbstractNetworkDetectorTest
{
    public function testFacebookProfile(): void
    {
        $network = $this->detect('https://www.facebook.com/profile.php?id=165046937419130');

        $this->assertEquals('facebook_profile', $network->getIdentifier());
    }
}
