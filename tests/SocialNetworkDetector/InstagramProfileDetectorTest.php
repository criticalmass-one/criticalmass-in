<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Tests\SocialNetworkDetector;

class InstagramProfileDetectorTest extends AbstractNetworkDetectorTest
{
    public function testInstagramProfile(): void
    {
        $network = $this->detect('https://www.instagram.com/criticalmasshamburg/');

        $this->assertEquals('instagram_profile', $network->getIdentifier());

        $network = $this->detect('http://www.instagram.com/criticalmasshamburg/');

        $this->assertEquals('instagram_profile', $network->getIdentifier());

        $network = $this->detect('https://instagram.com/criticalmasshamburg/');

        $this->assertEquals('instagram_profile', $network->getIdentifier());

        $network = $this->detect('http://www.instagram.com/criticalmasshamburg/');

        $this->assertEquals('instagram_profile', $network->getIdentifier());

        $network = $this->detect('https://www.instagram.com/criticalmasshamburg');

        $this->assertEquals('instagram_profile', $network->getIdentifier());

        $network = $this->detect('http://www.instagram.com/criticalmasshamburg');

        $this->assertEquals('instagram_profile', $network->getIdentifier());

        $network = $this->detect('https://instagram.com/criticalmasshamburg');

        $this->assertEquals('instagram_profile', $network->getIdentifier());

        $network = $this->detect('http://www.instagram.com/criticalmasshamburg');

        $this->assertEquals('instagram_profile', $network->getIdentifier());

        $network = $this->detect('http://www.instagram.de/criticalmassbremen');

        $this->assertEquals('instagram_profile', $network->getIdentifier());

        $network = $this->detect('http://www.instagram.fr/velorution');

        $this->assertEquals('instagram_profile', $network->getIdentifier());
    }
}
