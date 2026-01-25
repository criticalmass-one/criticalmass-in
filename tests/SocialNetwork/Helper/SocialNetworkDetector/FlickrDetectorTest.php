<?php declare(strict_types=1);

namespace Tests\SocialNetwork\Helper\SocialNetworkDetector;

class FlickrDetectorTest extends AbstractNetworkDetectorTestCase
{
    public function testFlickr(): void
    {
        $network = $this->detect('https://www.flickr.com/photos/130278554@N08/');

        $this->assertEquals('flickr', $network->getIdentifier());
    }
}
