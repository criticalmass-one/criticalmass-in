<?php declare(strict_types=1);

namespace Tests\SocialNetwork\Helper\SocialNetworkDetector;

class GoogleDetectorTest extends AbstractNetworkDetectorTestCase
{
    public function testGoogle(): void
    {
        $network = $this->detect('https://plus.google.com/+Critical-Mass-Hamburg');

        $this->assertEquals('google', $network->getIdentifier());
    }
}
