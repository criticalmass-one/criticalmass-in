<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Tests\Network;

use App\Criticalmass\SocialNetwork\Network\YoutubePlaylist;
use PHPUnit\Framework\TestCase;

class YoutubePlaylistTest extends TestCase
{
    public function testName(): void
    {
        $this->assertEquals('YouTube-Playlist', (new YoutubePlaylist())->getName());
    }

    public function testIdentifier(): void
    {
        $this->assertEquals('youtube_playlist', (new YoutubePlaylist())->getIdentifier());
    }

    public function testBackgroundcolor(): void
    {
        $this->assertEquals('rgb(255, 0, 0)', (new YoutubePlaylist())->getBackgroundColor());
    }

    public function testTextColor(): void
    {
        $this->assertEquals('white', (new YoutubePlaylist())->getTextColor());
    }

    public function testIcon(): void
    {
        $this->assertEquals('fab fa-youtube', (new YoutubePlaylist())->getIcon());
    }

    public function testDetectorPriority(): void
    {
        $this->assertEquals(0, (new YoutubePlaylist())->getDetectorPriority());
    }
}
