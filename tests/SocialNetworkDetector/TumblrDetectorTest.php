<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Tests\SocialNetworkDetector;

class TumblrDetectorTest extends AbstractNetworkDetectorTest
{
    public function testTumblr(): void
    {
        $network = $this->detect('https://criticalmasshamburg.tumblr.com/');

        $this->assertEquals('tumblr', $network->getIdentifier());

        $network = $this->detect('http://criticalmasshamburg.tumblr.com/');

        $this->assertEquals('tumblr', $network->getIdentifier());

        $network = $this->detect('https://www.criticalmasshamburg.tumblr.com/');

        $this->assertEquals('tumblr', $network->getIdentifier());

        $network = $this->detect('http://www.criticalmasshamburg.tumblr.com/');

        $this->assertEquals('tumblr', $network->getIdentifier());
    }
}
