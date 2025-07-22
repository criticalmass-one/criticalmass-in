<?php declare(strict_types=1);

namespace Tests\SocialNetworkDetector;

class GoogleDetectorTest extends AbstractNetworkDetectorTest
{
    public function testGoogle(): void
    {
        $network = $this->detect('https://plus.google.com/+Critical-Mass-Hamburg');

        $this->assertEquals('google', $network->getIdentifier());
    }
}
