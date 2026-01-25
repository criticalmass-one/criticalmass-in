<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Tests\Network;

use App\Criticalmass\SocialNetwork\Network\FacebookEvent;
use PHPUnit\Framework\TestCase;

class FacebookEventTest extends TestCase
{
    public function testName(): void
    {
        $this->assertEquals('Facebook-Event', (new FacebookEvent())->getName());
    }

    public function testIdentifier(): void
    {
        $this->assertEquals('facebook_event', (new FacebookEvent())->getIdentifier());
    }

    public function testBackgroundcolor(): void
    {
        $this->assertEquals('rgb(60, 88, 152)', (new FacebookEvent())->getBackgroundColor());
    }

    public function testTextColor(): void
    {
        $this->assertEquals('white', (new FacebookEvent())->getTextColor());
    }

    public function testIcon(): void
    {
        $this->assertEquals('fab fa-facebook-f', (new FacebookEvent())->getIcon());
    }

    public function testDetectorPriority(): void
    {
        $this->assertEquals(0, (new FacebookEvent())->getDetectorPriority());
    }
}
