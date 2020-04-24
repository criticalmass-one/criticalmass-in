<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Tests\SocialNetworkDetector;

class YoutubeChannelDetectorTest extends AbstractNetworkDetectorTest
{
    public function testYoutubeChannel(): void
    {
        $network = $this->detect('https://www.youtube.com/channel/UCq3Ci-h945sbEYXpVlw7rJg');

        $this->assertEquals('youtube_channel', $network->getIdentifier());

        $network = $this->detect('https://youtube.com/channel/UCq3Ci-h945sbEYXpVlw7rJg');

        $this->assertEquals('youtube_channel', $network->getIdentifier());

        $network = $this->detect('http://www.youtube.com/channel/UCq3Ci-h945sbEYXpVlw7rJg');

        $this->assertEquals('youtube_channel', $network->getIdentifier());

        $network = $this->detect('http://youtube.com/channel/UCq3Ci-h945sbEYXpVlw7rJg');

        $this->assertEquals('youtube_channel', $network->getIdentifier());
    }
}
