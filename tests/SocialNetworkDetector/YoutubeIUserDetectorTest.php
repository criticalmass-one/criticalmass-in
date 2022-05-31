<?php declare(strict_types=1);

namespace Tests\SocialNetworkDetector;

class YoutubeIUserDetectorTest extends AbstractNetworkDetectorTest
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
