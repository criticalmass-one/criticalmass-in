<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Tests\Network;

use App\Criticalmass\SocialNetwork\Network\YoutubeVideo;
use PHPUnit\Framework\TestCase;

class YoutubeVideoTest extends TestCase
{
    public function testName(): void
    {
        $this->assertEquals('YouTube-Video', (new YoutubeVideo())->getName());
    }

    public function testIdentifier(): void
    {
        $this->assertEquals('youtube_video', (new YoutubeVideo())->getIdentifier());
    }

    public function testBackgroundcolor(): void
    {
        $this->assertEquals('rgb(255, 0, 0)', (new YoutubeVideo())->getBackgroundColor());
    }

    public function testTextColor(): void
    {
        $this->assertEquals('white', (new YoutubeVideo())->getTextColor());
    }

    public function testIcon(): void
    {
        $this->assertEquals('fab fa-youtube', (new YoutubeVideo())->getIcon());
    }

    public function testDetectorPriority(): void
    {
        $this->assertEquals(-10, (new YoutubeVideo())->getDetectorPriority());
    }
}
