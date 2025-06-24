<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Tests\Network;

use App\Criticalmass\SocialNetwork\Network\BlueskyProfile;
use PHPUnit\Framework\TestCase;

class BlueskyProfileTest extends TestCase
{
    public function testName(): void
    {
        $this->assertEquals('Bluesky-Profil', (new BlueskyProfile())->getName());
    }

    public function testIdentifier(): void
    {
        $this->assertEquals('bluesky_profile', (new BlueskyProfile())->getIdentifier());
    }

    public function testBackgroundcolor(): void
    {
        $this->assertEquals('#0276ff', (new BlueskyProfile())->getBackgroundColor());
    }

    public function testTextColor(): void
    {
        $this->assertEquals('white', (new BlueskyProfile())->getTextColor());
    }

    public function testIcon(): void
    {
        $this->assertEquals('fab fa-bluesky', (new BlueskyProfile())->getIcon());
    }

    public function testDetectorPriority(): void
    {
        $this->assertEquals(0, (new BlueskyProfile())->getDetectorPriority());
    }
}
