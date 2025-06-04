<?php declare(strict_types=1);

namespace Tests\SocialNetworkDetector;

class InstagramPhotoDetectorTest extends AbstractNetworkDetectorTest
{
    public function testInstagramPhoto(): void
    {
        $network = $this->detect('https://www.instagram.com/p/BsRoT-eA23Q/');

        $this->assertEquals('instagram_photo', $network->getIdentifier());

        $network = $this->detect('http://www.instagram.com/p/BsRoT-eA23Q/');

        $this->assertEquals('instagram_photo', $network->getIdentifier());

        $network = $this->detect('https://instagram.com/p/BsRoT-eA23Q/');

        $this->assertEquals('instagram_photo', $network->getIdentifier());

        $network = $this->detect('http://instagram.com/p/BsRoT-eA23Q/');

        $this->assertEquals('instagram_photo', $network->getIdentifier());

        $network = $this->detect('https://www.instagram.com/p/BsRoT-eA23Q');

        $this->assertEquals('instagram_photo', $network->getIdentifier());

        $network = $this->detect('http://www.instagram.com/p/BsRoT-eA23Q');

        $this->assertEquals('instagram_photo', $network->getIdentifier());

        $network = $this->detect('https://instagram.com/p/BsRoT-eA23Q');

        $this->assertEquals('instagram_photo', $network->getIdentifier());

        $network = $this->detect('http://instagram.com/p/BsRoT-eA23Q');

        $this->assertEquals('instagram_photo', $network->getIdentifier());
    }
}
