<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Tests\Network;

use App\Criticalmass\SocialNetwork\Network\Flickr;
use PHPUnit\Framework\TestCase;

class FlickrTest extends TestCase
{
    public function testName(): void
    {
        $this->assertEquals('flickr', (new Flickr())->getName());
    }

    public function testIdentifier(): void
    {
        $this->assertEquals('flickr', (new Flickr())->getIdentifier());
    }

    public function testBackgroundcolor(): void
    {
        $this->assertEquals('rgb(12, 101, 211)', (new Flickr())->getBackgroundColor());
    }

    public function testTextColor(): void
    {
        $this->assertEquals('white', (new Flickr())->getTextColor());
    }

    public function testIcon(): void
    {
        $this->assertEquals('fab fa-flickr', (new Flickr())->getIcon());
    }

    public function testDetectorPriority(): void
    {
        $this->assertEquals(0, (new Flickr())->getDetectorPriority());
    }
}
