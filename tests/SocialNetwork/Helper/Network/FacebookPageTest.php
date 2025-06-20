<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Tests\Network;

use App\Criticalmass\SocialNetwork\Network\FacebookPage;
use PHPUnit\Framework\TestCase;

class FacebookPageTest extends TestCase
{
    public function testName(): void
    {
        $this->assertEquals('facebook-Seite', (new FacebookPage())->getName());
    }

    public function testIdentifier(): void
    {
        $this->assertEquals('facebook_page', (new FacebookPage())->getIdentifier());
    }

    public function testBackgroundcolor(): void
    {
        $this->assertEquals('rgb(60, 88, 152)', (new FacebookPage())->getBackgroundColor());
    }

    public function testTextColor(): void
    {
        $this->assertEquals('white', (new FacebookPage())->getTextColor());
    }

    public function testIcon(): void
    {
        $this->assertEquals('fab fa-facebook-f', (new FacebookPage())->getIcon());
    }

    public function testDetectorPriority(): void
    {
        $this->assertEquals(0, (new FacebookPage())->getDetectorPriority());
    }
}
