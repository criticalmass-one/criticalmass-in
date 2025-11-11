<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Tests\Network;

use App\Criticalmass\SocialNetwork\Network\Google;
use PHPUnit\Framework\TestCase;

class GoogleTest extends TestCase
{
    public function testName(): void
    {
        $this->assertEquals('Google+', (new Google())->getName());
    }

    public function testIdentifier(): void
    {
        $this->assertEquals('google', (new Google())->getIdentifier());
    }

    public function testBackgroundcolor(): void
    {
        $this->assertEquals('rgb(234, 66, 53)', (new Google())->getBackgroundColor());
    }

    public function testTextColor(): void
    {
        $this->assertEquals('white', (new Google())->getTextColor());
    }

    public function testIcon(): void
    {
        $this->assertEquals('fab fa-google-plus-g', (new Google())->getIcon());
    }

    public function testDetectorPriority(): void
    {
        $this->assertEquals(0, (new Google())->getDetectorPriority());
    }
}
