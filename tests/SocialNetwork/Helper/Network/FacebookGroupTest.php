<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Tests\Network;

use App\Criticalmass\SocialNetwork\Network\FacebookGroup;
use PHPUnit\Framework\TestCase;

class FacebookGroupTest extends TestCase
{
    public function testName(): void
    {
        $this->assertEquals('facebook-Gruppe', (new FacebookGroup())->getName());
    }

    public function testIdentifier(): void
    {
        $this->assertEquals('facebook_group', (new FacebookGroup())->getIdentifier());
    }

    public function testBackgroundcolor(): void
    {
        $this->assertEquals('rgb(60, 88, 152)', (new FacebookGroup())->getBackgroundColor());
    }

    public function testTextColor(): void
    {
        $this->assertEquals('white', (new FacebookGroup())->getTextColor());
    }

    public function testIcon(): void
    {
        $this->assertEquals('fab fa-facebook-f', (new FacebookGroup())->getIcon());
    }

    public function testDetectorPriority(): void
    {
        $this->assertEquals(0, (new FacebookGroup())->getDetectorPriority());
    }
}
