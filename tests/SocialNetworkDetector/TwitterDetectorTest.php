<?php declare(strict_types=1);

namespace Tests\SocialNetworkDetector;

class TwitterDetectorTest extends AbstractNetworkDetectorTest
{
    public function testTwitter(): void
    {
        $network = $this->detect('https://twitter.com/cm_hh');

        $this->assertEquals('twitter', $network->getIdentifier());

        $network = $this->detect('https://www.twitter.com/cm_hh');

        $this->assertEquals('twitter', $network->getIdentifier());

        $network = $this->detect('http://twitter.com/cm_hh');

        $this->assertEquals('twitter', $network->getIdentifier());

        $network = $this->detect('http://www.twitter.com/cm_hh');

        $this->assertEquals('twitter', $network->getIdentifier());

        $network = $this->detect('@cm_hh');

        $this->assertNull($network);
    }
}
