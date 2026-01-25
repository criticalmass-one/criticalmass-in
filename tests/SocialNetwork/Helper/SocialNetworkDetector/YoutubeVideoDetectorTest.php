<?php declare(strict_types=1);

namespace Tests\SocialNetwork\Helper\SocialNetworkDetector;

class YoutubeVideoDetectorTest extends AbstractNetworkDetectorTestCase
{
    public function testYoutubeVideo(): void
    {
        $network = $this->detect('https://www.youtube.com/watch?v=MglnNn_rB3I');

        $this->assertEquals('youtube_video', $network->getIdentifier());
    }
}
