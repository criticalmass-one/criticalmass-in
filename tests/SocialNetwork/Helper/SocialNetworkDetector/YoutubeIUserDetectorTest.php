<?php declare(strict_types=1);

namespace Tests\SocialNetwork\Helper\SocialNetworkDetector;

class YoutubeIUserDetectorTest extends AbstractNetworkDetectorTestCase
{
    public function testYoutubeUser(): void
    {
        $network = $this->detect('https://www.youtube.com/user/TomorrowlandChannel/');

        $this->assertEquals('youtube_user', $network->getIdentifier());

        $network = $this->detect('http://www.youtube.com/user/TomorrowlandChannel/');

        $this->assertEquals('youtube_user', $network->getIdentifier());

        $network = $this->detect('https://youtube.com/user/TomorrowlandChannel/');

        $this->assertEquals('youtube_user', $network->getIdentifier());

        $network = $this->detect('http://youtube.com/user/TomorrowlandChannel/');

        $this->assertEquals('youtube_user', $network->getIdentifier());

        $network = $this->detect('https://www.youtube.com/user/TomorrowlandChannel');

        $this->assertEquals('youtube_user', $network->getIdentifier());

        $network = $this->detect('http://www.youtube.com/user/TomorrowlandChannel');

        $this->assertEquals('youtube_user', $network->getIdentifier());

        $network = $this->detect('https://youtube.com/user/TomorrowlandChannel');

        $this->assertEquals('youtube_user', $network->getIdentifier());

        $network = $this->detect('http://youtube.com/user/TomorrowlandChannel');

        $this->assertEquals('youtube_user', $network->getIdentifier());
    }
}
