<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Tests\Network;

use App\Criticalmass\SocialNetwork\Network\FacebookProfile;
use PHPUnit\Framework\TestCase;

class FacebookProfileTest extends TestCase
{
    public function testName(): void
    {
        $this->assertEquals('facebook-Profil', (new FacebookProfile())->getName());
    }

    public function testIdentifier(): void
    {
        $this->assertEquals('facebook_profile', (new FacebookProfile())->getIdentifier());
    }

    public function testBackgroundcolor(): void
    {
        $this->assertEquals('rgb(60, 88, 152)', (new FacebookProfile())->getBackgroundColor());
    }

    public function testTextColor(): void
    {
        $this->assertEquals('white', (new FacebookProfile())->getTextColor());
    }

    public function testIcon(): void
    {
        $this->assertEquals('fab fa-facebook-f', (new FacebookProfile())->getIcon());
    }

    public function testDetectorPriority(): void
    {
        $this->assertEquals(0, (new FacebookProfile())->getDetectorPriority());
    }
}
