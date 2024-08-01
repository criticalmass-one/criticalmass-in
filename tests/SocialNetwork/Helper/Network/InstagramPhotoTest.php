<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Tests\Network;

use App\Criticalmass\SocialNetwork\Network\InstagramPhoto;
use PHPUnit\Framework\TestCase;

class InstagramPhotoTest extends TestCase
{
    public function testName(): void
    {
        $this->assertEquals('Instagram-Foto', (new InstagramPhoto())->getName());
    }

    public function testIdentifier(): void
    {
        $this->assertEquals('instagram_photo', (new InstagramPhoto())->getIdentifier());
    }

    public function testBackgroundcolor(): void
    {
        $this->assertEquals('rgb(203, 44, 128)', (new InstagramPhoto())->getBackgroundColor());
    }

    public function testTextColor(): void
    {
        $this->assertEquals('white', (new InstagramPhoto())->getTextColor());
    }

    public function testIcon(): void
    {
        $this->assertEquals('fab fa-instagram', (new InstagramPhoto())->getIcon());
    }

    public function testDetectorPriority(): void
    {
        $this->assertEquals(0, (new InstagramPhoto())->getDetectorPriority());
    }
}
