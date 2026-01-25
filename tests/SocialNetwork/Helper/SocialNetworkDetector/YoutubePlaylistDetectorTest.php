<?php declare(strict_types=1);

namespace Tests\SocialNetwork\Helper\SocialNetworkDetector;

class YoutubePlaylistDetectorTest extends AbstractNetworkDetectorTestCase
{
    public function testYoutubePlaylist(): void
    {
        $network = $this->detect('https://www.youtube.com/playlist?list=abcdefg');

        $this->assertEquals('youtube_playlist', $network->getIdentifier());

        $network = $this->detect('https://youtube.com/playlist?list=abcdefg');

        $this->assertEquals('youtube_playlist', $network->getIdentifier());

        $network = $this->detect('http://www.youtube.com/playlist?list=abcdefg');

        $this->assertEquals('youtube_playlist', $network->getIdentifier());

        $network = $this->detect('http://youtube.com/playlist?list=abcdefg');

        $this->assertEquals('youtube_playlist', $network->getIdentifier());
    }
}
