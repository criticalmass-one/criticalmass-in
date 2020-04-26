<?php declare(strict_types=1);

namespace Tests\SocialNetworkDetector;

class YoutubeVideoDetectorTest extends AbstractNetworkDetectorTest
{
    public function testYoutubeVideo(): void
    {
        $network = $this->detect('https://www.youtube.com/watch?v=MglnNn_rB3I');

        $this->assertEquals('youtube_video', $network->getIdentifier());
    }
}
