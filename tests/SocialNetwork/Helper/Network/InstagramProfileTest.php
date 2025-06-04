<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Tests\Network;

use App\Criticalmass\SocialNetwork\Network\InstagramProfile;
use PHPUnit\Framework\TestCase;

class InstagramProfileTest extends TestCase
{
    public function testName(): void
    {
        $this->assertEquals('Instagram-Profil', (new InstagramProfile())->getName());
    }

    public function testIdentifier(): void
    {
        $this->assertEquals('instagram_profile', (new InstagramProfile())->getIdentifier());
    }

    public function testBackgroundcolor(): void
    {
        $this->assertEquals('rgb(203, 44, 128)', (new InstagramProfile())->getBackgroundColor());
    }

    public function testTextColor(): void
    {
        $this->assertEquals('white', (new InstagramProfile())->getTextColor());
    }

    public function testIcon(): void
    {
        $this->assertEquals('fab fa-instagram', (new InstagramProfile())->getIcon());
    }

    public function testDetectorPriority(): void
    {
        $this->assertEquals(0, (new InstagramProfile())->getDetectorPriority());
    }
}
